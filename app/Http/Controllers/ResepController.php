<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use App\Models\Pendaftaran;
use App\Models\Obat;
use App\Models\DetailResep;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $resep = collect();

        if ($user->role === 'admin') {
            // Admin: lihat semua resep
            $resep = Resep::with(['pasien', 'dokter', 'pendaftaran'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        } elseif ($user->role === 'dokter') {
            // Dokter: lihat resep yang dibuatnya
            $resep = Resep::with(['pasien', 'pendaftaran'])
                ->where('dokter_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return view('resep.index', compact('resep'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->role !== 'dokter') {
            return redirect()->route('resep.index')
                ->with('error', 'Hanya dokter yang dapat membuat resep');
        }

        // Ambil pendaftaran yang sudah selesai pemeriksaan
        $pendaftaran = Pendaftaran::with(['pasien', 'poli'])
            ->where('dokter_id', $user->id)
            ->where('status_pendaftaran', 'Selesai')
            ->whereDoesntHave('resep', function ($query) {
                $query->where('status_resep', '!=', 'Batal');
            })
            ->orderBy('jam_pendaftaran', 'desc')
            ->get();

        $obat = Obat::aktif()->stokTersedia()->orderBy('nama_obat')->get();

        return view('resep.create', compact('pendaftaran', 'obat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pendaftaran_id' => 'required|exists:pendaftaran,id',
            'diagnosa' => 'required|string|max:500',
            'catatan_dokter' => 'nullable|string|max:1000',
            'obat' => 'required|array|min:1',
            'obat.*.obat_id' => 'required|exists:obat,id',
            'obat.*.jumlah' => 'required|integer|min:1',
            'obat.*.aturan_pakai' => 'required|string|max:200',
            'obat.*.catatan' => 'nullable|string|max:200',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah pendaftaran sudah ada resep
            $existingResep = Resep::where('pendaftaran_id', $request->pendaftaran_id)
                ->where('status_resep', '!=', 'Batal')
                ->first();

            if ($existingResep) {
                return redirect()->back()
                    ->with('error', 'Pendaftaran ini sudah memiliki resep')
                    ->withInput();
            }

            // Ambil data pendaftaran
            $pendaftaran = Pendaftaran::with(['pasien'])->find($request->pendaftaran_id);

            // Buat resep
            $resep = Resep::create([
                'no_resep' => Resep::generateNoResep(),
                'pendaftaran_id' => $request->pendaftaran_id,
                'pasien_id' => $pendaftaran->pasien_id,
                'dokter_id' => auth()->id(),
                'tanggal_resep' => today(),
                'diagnosa' => $request->diagnosa,
                'catatan_dokter' => $request->catatan_dokter,
                'status_resep' => 'Draft',
                'created_by' => auth()->id(),
            ]);

            // Buat detail resep
            foreach ($request->obat as $obatData) {
                $obat = Obat::find($obatData['obat_id']);
                
                // Cek stok
                if (!$obat->isStokCukup($obatData['jumlah'])) {
                    throw new \Exception("Stok obat {$obat->nama_obat} tidak mencukupi");
                }

                DetailResep::create([
                    'resep_id' => $resep->id,
                    'obat_id' => $obatData['obat_id'],
                    'jumlah' => $obatData['jumlah'],
                    'aturan_pakai' => $obatData['aturan_pakai'],
                    'harga_satuan' => $obat->harga,
                    'catatan' => $obatData['catatan'] ?? null,
                ]);

                // Kurangi stok
                $obat->updateStok($obatData['jumlah'], 'kurang');
            }

            // Hitung total harga
            $resep->calculateTotalHarga();

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'resep',
                'Resep baru dibuat untuk pasien: ' . $pendaftaran->pasien->nama_pasien,
                'resep',
                $resep->id
            );

            DB::commit();

            return redirect()->route('resep.show', $resep->id)
                ->with('success', 'Resep berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Resep $resep)
    {
        $resep->load(['pasien', 'dokter', 'pendaftaran', 'detailResep.obat', 'activities']);
        return view('resep.show', compact('resep'));
    }

    public function edit(Resep $resep)
    {
        if (!$resep->isEditable()) {
            return redirect()->route('resep.show', $resep->id)
                ->with('error', 'Resep tidak dapat diedit');
        }

        $obat = Obat::aktif()->stokTersedia()->orderBy('nama_obat')->get();
        return view('resep.edit', compact('resep', 'obat'));
    }

    public function update(Request $request, Resep $resep)
    {
        if (!$resep->isEditable()) {
            return redirect()->route('resep.show', $resep->id)
                ->with('error', 'Resep tidak dapat diedit');
        }

        $request->validate([
            'diagnosa' => 'required|string|max:500',
            'catatan_dokter' => 'nullable|string|max:1000',
            'obat' => 'required|array|min:1',
            'obat.*.obat_id' => 'required|exists:obat,id',
            'obat.*.jumlah' => 'required|integer|min:1',
            'obat.*.aturan_pakai' => 'required|string|max:200',
            'obat.*.catatan' => 'nullable|string|max:200',
        ]);

        try {
            DB::beginTransaction();

            // Update resep
            $resep->update([
                'diagnosa' => $request->diagnosa,
                'catatan_dokter' => $request->catatan_dokter,
                'updated_by' => auth()->id(),
            ]);

            // Hapus detail resep lama dan kembalikan stok
            foreach ($resep->detailResep as $detail) {
                $obat = $detail->obat;
                $obat->updateStok($detail->jumlah, 'tambah');
                $detail->delete();
            }

            // Buat detail resep baru
            foreach ($request->obat as $obatData) {
                $obat = Obat::find($obatData['obat_id']);
                
                // Cek stok
                if (!$obat->isStokCukup($obatData['jumlah'])) {
                    throw new \Exception("Stok obat {$obat->nama_obat} tidak mencukupi");
                }

                DetailResep::create([
                    'resep_id' => $resep->id,
                    'obat_id' => $obatData['obat_id'],
                    'jumlah' => $obatData['jumlah'],
                    'aturan_pakai' => $obatData['aturan_pakai'],
                    'harga_satuan' => $obat->harga,
                    'catatan' => $obatData['catatan'] ?? null,
                ]);

                // Kurangi stok
                $obat->updateStok($obatData['jumlah'], 'kurang');
            }

            // Hitung total harga
            $resep->calculateTotalHarga();

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'resep',
                'Resep diperbarui untuk pasien: ' . $resep->pasien->nama_pasien,
                'resep',
                $resep->id
            );

            DB::commit();

            return redirect()->route('resep.show', $resep->id)
                ->with('success', 'Resep berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Resep $resep)
    {
        if (!$resep->isEditable()) {
            return redirect()->route('resep.show', $resep->id)
                ->with('error', 'Resep tidak dapat dihapus');
        }

        try {
            DB::beginTransaction();

            // Kembalikan stok
            foreach ($resep->detailResep as $detail) {
                $obat = $detail->obat;
                $obat->updateStok($detail->jumlah, 'tambah');
            }

            // Log activity sebelum dihapus
            ActivityLog::log(
                auth()->id(),
                'resep',
                'Resep dibatalkan untuk pasien: ' . $resep->pasien->nama_pasien,
                'resep',
                $resep->id
            );

            $resep->delete();

            DB::commit();

            return redirect()->route('resep.index')
                ->with('success', 'Resep berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Resep $resep)
    {
        $request->validate([
            'status' => 'required|in:Draft,Aktif,Selesai,Batal'
        ]);

        $resep->updateStatus($request->status, auth()->id());

        return redirect()->back()
            ->with('success', 'Status resep berhasil diperbarui');
    }

    public function print(Resep $resep)
    {
        $resep->load(['pasien', 'dokter', 'pendaftaran', 'detailResep.obat']);
        return view('resep.print', compact('resep'));
    }

    public function getObatByKategori(Request $request)
    {
        $kategori = $request->get('kategori');
        
        $obat = Obat::aktif()
            ->stokTersedia()
            ->when($kategori, function ($query) use ($kategori) {
                return $query->where('kategori', $kategori);
            })
            ->orderBy('nama_obat')
            ->get(['id', 'nama_obat', 'kode_obat', 'satuan', 'dosis', 'stok', 'harga']);

        return response()->json($obat);
    }
}
