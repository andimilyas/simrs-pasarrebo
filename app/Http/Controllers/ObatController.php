<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatController extends Controller
{
    public function index()
    {
        $obat = Obat::with(['createdBy'])
            ->orderBy('nama_obat')
            ->paginate(15);

        return view('obat.index', compact('obat'));
    }

    public function create()
    {
        $kategoris = [
            'Antibiotik' => 'Antibiotik',
            'Analgesik' => 'Analgesik',
            'Antipiretik' => 'Antipiretik',
            'Vitamin' => 'Vitamin',
            'Suplemen' => 'Suplemen',
            'Obat Luar' => 'Obat Luar',
            'Lainnya' => 'Lainnya',
        ];

        $satuans = [
            'Tablet' => 'Tablet',
            'Kapsul' => 'Kapsul',
            'Syrup' => 'Syrup',
            'Injeksi' => 'Injeksi',
            'Salep' => 'Salep',
            'Drops' => 'Drops',
            'Lainnya' => 'Lainnya',
        ];

        return view('obat.create', compact('kategoris', 'satuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'dosis' => 'required|string|max:100',
            'indikasi' => 'required|string|max:500',
            'kontraindikasi' => 'nullable|string|max:500',
            'efek_samping' => 'nullable|string|max:500',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'minimal_stok' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $obat = Obat::create([
                'kode_obat' => Obat::generateKodeObat(),
                'nama_obat' => $request->nama_obat,
                'kategori' => $request->kategori,
                'satuan' => $request->satuan,
                'dosis' => $request->dosis,
                'indikasi' => $request->indikasi,
                'kontraindikasi' => $request->kontraindikasi,
                'efek_samping' => $request->efek_samping,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'minimal_stok' => $request->minimal_stok,
                'status' => 'Aktif',
                'created_by' => auth()->id(),
            ]);

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'create',
                'Obat baru ditambahkan: ' . $obat->nama_obat,
                'obat',
                $obat->id
            );

            DB::commit();

            return redirect()->route('obat.index')
                ->with('success', 'Obat berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Obat $obat)
    {
        $obat->load(['createdBy', 'updatedBy']);
        return view('obat.show', compact('obat'));
    }

    public function edit(Obat $obat)
    {
        $kategoris = [
            'Antibiotik' => 'Antibiotik',
            'Analgesik' => 'Analgesik',
            'Antipiretik' => 'Antipiretik',
            'Vitamin' => 'Vitamin',
            'Suplemen' => 'Suplemen',
            'Obat Luar' => 'Obat Luar',
            'Lainnya' => 'Lainnya',
        ];

        $satuans = [
            'Tablet' => 'Tablet',
            'Kapsul' => 'Kapsul',
            'Syrup' => 'Syrup',
            'Injeksi' => 'Injeksi',
            'Salep' => 'Salep',
            'Drops' => 'Drops',
            'Lainnya' => 'Lainnya',
        ];

        return view('obat.edit', compact('obat', 'kategoris', 'satuans'));
    }

    public function update(Request $request, Obat $obat)
    {
        $request->validate([
            'nama_obat' => 'required|string|max:255',
            'kategori' => 'required|string|max:100',
            'satuan' => 'required|string|max:50',
            'dosis' => 'required|string|max:100',
            'indikasi' => 'required|string|max:500',
            'kontraindikasi' => 'nullable|string|max:500',
            'efek_samping' => 'nullable|string|max:500',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'minimal_stok' => 'required|integer|min:0',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        try {
            DB::beginTransaction();

            $obat->update([
                'nama_obat' => $request->nama_obat,
                'kategori' => $request->kategori,
                'satuan' => $request->satuan,
                'dosis' => $request->dosis,
                'indikasi' => $request->indikasi,
                'kontraindikasi' => $request->kontraindikasi,
                'efek_samping' => $request->efek_samping,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'minimal_stok' => $request->minimal_stok,
                'status' => $request->status,
                'updated_by' => auth()->id(),
            ]);

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'update',
                'Data obat diperbarui: ' . $obat->nama_obat,
                'obat',
                $obat->id
            );

            DB::commit();

            return redirect()->route('obat.index')
                ->with('success', 'Obat berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Obat $obat)
    {
        try {
            DB::beginTransaction();

            // Log activity sebelum dihapus
            ActivityLog::log(
                auth()->id(),
                'delete',
                'Obat dihapus: ' . $obat->nama_obat,
                'obat',
                $obat->id
            );

            $obat->delete();

            DB::commit();

            return redirect()->route('obat.index')
                ->with('success', 'Obat berhasil dihapus');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateStok(Request $request, Obat $obat)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tipe' => 'required|in:tambah,kurang',
        ]);

        try {
            DB::beginTransaction();

            $jumlah = $request->jumlah;
            $tipe = $request->tipe;

            if ($tipe === 'kurang' && !$obat->isStokCukup($jumlah)) {
                return redirect()->back()
                    ->with('error', 'Stok tidak mencukupi');
            }

            $obat->updateStok($jumlah, $tipe);

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'update',
                'Stok obat ' . $obat->nama_obat . ' diubah: ' . $tipe . ' ' . $jumlah,
                'obat',
                $obat->id
            );

            DB::commit();

            return redirect()->back()
                ->with('success', 'Stok obat berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $kategori = $request->get('kategori');
        $status = $request->get('status');

        $obat = Obat::query();

        if ($query) {
            $obat->where(function ($q) use ($query) {
                $q->where('nama_obat', 'like', "%{$query}%")
                  ->orWhere('kode_obat', 'like', "%{$query}%")
                  ->orWhere('kategori', 'like', "%{$query}%");
            });
        }

        if ($kategori) {
            $obat->where('kategori', $kategori);
        }

        if ($status) {
            $obat->where('status', $status);
        }

        $obat = $obat->with(['createdBy'])
            ->orderBy('nama_obat')
            ->paginate(15);

        return view('obat.index', compact('obat', 'query', 'kategori', 'status'));
    }
}
