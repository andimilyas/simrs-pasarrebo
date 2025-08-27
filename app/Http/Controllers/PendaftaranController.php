<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Pasien;
use App\Models\Poli;
use App\Models\Tagihan;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendaftaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $pendaftaran = collect();

        if ($user->role === 'admin') {
            // Admin: lihat semua pendaftaran harian
            $pendaftaran = Pendaftaran::with(['pasien', 'dokter', 'poli'])
                ->hariIni()
                ->orderBy('jam_pendaftaran', 'asc')
                ->paginate(15);
        } elseif ($user->role === 'dokter') {
            // Dokter: lihat daftar pasien yang mendaftar ke polinya
            $pendaftaran = Pendaftaran::with(['pasien', 'poli'])
                ->where('dokter_id', $user->id)
                ->whereDate('tanggal_pendaftaran', today())
                ->orderBy('jam_pendaftaran', 'asc')
                ->paginate(15);
        } elseif ($user->role === 'petugas') {
            // Petugas: lihat pendaftaran yang dibuatnya
            $pendaftaran = Pendaftaran::with(['pasien', 'dokter', 'poli'])
                ->where('created_by', $user->id)
                ->whereDate('tanggal_pendaftaran', today())
                ->orderBy('jam_pendaftaran', 'asc')
                ->paginate(15);
        }

        return view('pendaftaran.index', compact('pendaftaran'));
    }

    public function create()
    {
        $pasiens = Pasien::orderBy('nama_pasien')->get();
        $polis = Poli::aktif()->get();
        $dokters = User::where('role', 'dokter')->orderBy('name')->get();
        
        return view('pendaftaran.create', compact('pasiens', 'polis', 'dokters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasien,id',
            'dokter_id' => 'required|exists:users,id',
            'poli_id' => 'required|exists:poli,id',
            'tanggal_pendaftaran' => 'required|date|after_or_equal:today',
            'jam_pendaftaran' => 'required',
            'jenis_pendaftaran' => 'required|in:Umum,BPJS,Asuransi',
            'keluhan' => 'required|string|max:500',
            'prioritas' => 'required|in:Normal,Urgent,Emergency',
            'catatan_petugas' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            // Cek kapasitas poli
            $poli = Poli::find($request->poli_id);
            $pendaftaranHariIni = Pendaftaran::where('poli_id', $request->poli_id)
                ->whereDate('tanggal_pendaftaran', $request->tanggal_pendaftaran)
                ->count();

            if ($pendaftaranHariIni >= $poli->kapasitas_harian) {
                return redirect()->back()
                    ->with('error', 'Kapasitas poli sudah penuh untuk tanggal tersebut')
                    ->withInput();
            }

            // Buat pendaftaran
            $pendaftaran = Pendaftaran::create([
                'no_pendaftaran' => Pendaftaran::generateNoPendaftaran(),
                'pasien_id' => $request->pasien_id,
                'dokter_id' => $request->dokter_id,
                'poli_id' => $request->poli_id,
                'tanggal_pendaftaran' => $request->tanggal_pendaftaran,
                'jam_pendaftaran' => $request->tanggal_pendaftaran . ' ' . $request->jam_pendaftaran,
                'jenis_pendaftaran' => $request->jenis_pendaftaran,
                'status_pendaftaran' => 'Terdaftar',
                'keluhan' => $request->keluhan,
                'prioritas' => $request->prioritas,
                'estimasi_waktu' => $this->calculateEstimasiWaktu($request->jam_pendaftaran, $request->prioritas),
                'catatan_petugas' => $request->catatan_petugas,
                'created_by' => auth()->id(),
            ]);

            // Buat tagihan otomatis
            $this->createTagihan($pendaftaran);

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'pendaftaran',
                'Pendaftaran baru dibuat untuk pasien: ' . $pendaftaran->pasien->nama_pasien,
                'pendaftaran',
                $pendaftaran->id
            );

            DB::commit();

            return redirect()->route('pendaftaran.index')
                ->with('success', 'Pendaftaran berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Pendaftaran $pendaftaran)
    {
        $pendaftaran->load(['pasien', 'dokter', 'poli', 'tagihan', 'activities']);
        return view('pendaftaran.show', compact('pendaftaran'));
    }

    public function edit(Pendaftaran $pendaftaran)
    {
        $pasiens = Pasien::orderBy('nama_pasien')->get();
        $polis = Poli::aktif()->get();
        $dokters = User::where('role', 'dokter')->orderBy('name')->get();
        
        return view('pendaftaran.edit', compact('pendaftaran', 'pasiens', 'polis', 'dokters'));
    }

    public function update(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasien,id',
            'dokter_id' => 'required|exists:users,id',
            'poli_id' => 'required|exists:poli,id',
            'tanggal_pendaftaran' => 'required|date',
            'jam_pendaftaran' => 'required',
            'jenis_pendaftaran' => 'required|in:Umum,BPJS,Asuransi',
            'keluhan' => 'required|string|max:500',
            'prioritas' => 'required|in:Normal,Urgent,Emergency',
            'catatan_petugas' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $pendaftaran->update([
                'pasien_id' => $request->pasien_id,
                'dokter_id' => $request->dokter_id,
                'poli_id' => $request->poli_id,
                'tanggal_pendaftaran' => $request->tanggal_pendaftaran,
                'jam_pendaftaran' => $request->tanggal_pendaftaran . ' ' . $request->jam_pendaftaran,
                'jenis_pendaftaran' => $request->jenis_pendaftaran,
                'keluhan' => $request->keluhan,
                'prioritas' => $request->prioritas,
                'estimasi_waktu' => $this->calculateEstimasiWaktu($request->jam_pendaftaran, $request->prioritas),
                'catatan_petugas' => $request->catatan_petugas,
                'updated_by' => auth()->id(),
            ]);

            // Update tagihan jika ada perubahan
            if ($pendaftaran->tagihan) {
                $this->updateTagihan($pendaftaran);
            }

            // Log activity
            ActivityLog::log(
                auth()->id(),
                'pendaftaran',
                'Data pendaftaran diperbarui untuk pasien: ' . $pendaftaran->pasien->nama_pasien,
                'pendaftaran',
                $pendaftaran->id
            );

            DB::commit();

            return redirect()->route('pendaftaran.index')
                ->with('success', 'Pendaftaran berhasil diperbarui');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Pendaftaran $pendaftaran)
    {
        try {
            DB::beginTransaction();

            // Hapus tagihan jika ada
            if ($pendaftaran->tagihan) {
                $pendaftaran->tagihan->delete();
            }

            // Log activity sebelum dihapus
            ActivityLog::log(
                auth()->id(),
                'pendaftaran',
                'Pendaftaran dibatalkan untuk pasien: ' . $pendaftaran->pasien->nama_pasien,
                'pendaftaran',
                $pendaftaran->id
            );

            $pendaftaran->delete();

            DB::commit();

            return redirect()->route('pendaftaran.index')
                ->with('success', 'Pendaftaran berhasil dibatalkan');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Pendaftaran $pendaftaran)
    {
        $request->validate([
            'status' => 'required|in:Terdaftar,Dalam Antrian,Sedang Diperiksa,Selesai,Batal'
        ]);

        $pendaftaran->updateStatus($request->status, auth()->id());

        return redirect()->back()
            ->with('success', 'Status pendaftaran berhasil diperbarui');
    }

    public function antrian()
    {
        $user = auth()->user();
        $antrian = collect();

        if ($user->role === 'dokter') {
            $antrian = Pendaftaran::with(['pasien', 'poli'])
                ->where('dokter_id', $user->id)
                ->whereDate('tanggal_pendaftaran', today())
                ->whereIn('status_pendaftaran', ['Terdaftar', 'Dalam Antrian'])
                ->orderBy('jam_pendaftaran', 'asc')
                ->get();
        } elseif ($user->role === 'admin') {
            $antrian = Pendaftaran::with(['pasien', 'dokter', 'poli'])
                ->whereDate('tanggal_pendaftaran', today())
                ->whereIn('status_pendaftaran', ['Terdaftar', 'Dalam Antrian'])
                ->orderBy('jam_pendaftaran', 'asc')
                ->get();
        }

        return view('pendaftaran.antrian', compact('antrian'));
    }

    // Private methods
    private function calculateEstimasiWaktu($jamPendaftaran, $prioritas)
    {
        $baseTime = Carbon::parse($jamPendaftaran);
        
        switch ($prioritas) {
            case 'Emergency':
                return $baseTime->addMinutes(15);
            case 'Urgent':
                return $baseTime->addMinutes(30);
            default:
                return $baseTime->addMinutes(60);
        }
    }

    private function createTagihan($pendaftaran)
    {
        $biayaPendaftaran = 50000;
        $biayaPoli = $pendaftaran->poli->biaya ?? 0;
        $biayaDokter = $pendaftaran->dokter->biaya_konsultasi ?? 0;

        $tagihan = Tagihan::create([
            'no_tagihan' => Tagihan::generateNoTagihan(),
            'pendaftaran_id' => $pendaftaran->id,
            'pasien_id' => $pendaftaran->pasien_id,
            'biaya_pendaftaran' => $biayaPendaftaran,
            'biaya_poli' => $biayaPoli,
            'biaya_dokter' => $biayaDokter,
            'biaya_tambahan' => 0,
            'diskon' => 0,
            'status_pembayaran' => 'Belum Bayar',
            'created_by' => auth()->id(),
        ]);

        $tagihan->calculateTotal();
    }

    private function updateTagihan($pendaftaran)
    {
        if ($pendaftaran->tagihan) {
            $biayaPoli = $pendaftaran->poli->biaya ?? 0;
            $biayaDokter = $pendaftaran->dokter->biaya_konsultasi ?? 0;

            $pendaftaran->tagihan->update([
                'biaya_poli' => $biayaPoli,
                'biaya_dokter' => $biayaDokter,
                'updated_by' => auth()->id(),
            ]);

            $pendaftaran->tagihan->calculateTotal();
        }
    }
}
