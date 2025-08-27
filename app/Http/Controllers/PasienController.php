<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasienController extends Controller
{
    public function index()
    {
        $pasiens = Pasien::orderBy('created_at', 'desc')->paginate(10);
        return view('pasien.index', compact('pasiens'));
    }

    public function create()
    {
        return view('pasien.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_mr' => 'required|unique:tbl_pasien,no_mr|max:20',
            'nama_pasien' => 'required|max:100',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|max:200',
            'tgl_daftar' => 'required|date',
            'no_bpjs' => 'nullable|max:30',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // 2MB max
        ], [
            'no_mr.required' => 'Nomor MR wajib diisi',
            'no_mr.unique' => 'Nomor MR sudah digunakan',
            'nama_pasien.required' => 'Nama pasien wajib diisi',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'tgl_daftar.required' => 'Tanggal daftar wajib diisi',
            'dokumen.mimes' => 'File dokumen harus berformat PDF, JPG, JPEG, atau PNG',
            'dokumen.max' => 'Ukuran file dokumen maksimal 2MB',
        ]);

        $data = $request->except('dokumen');
        
        // Handle file upload
        if ($request->hasFile('dokumen')) {
            $file = $request->file('dokumen');
            $fileName = 'pasien_' . $request->no_mr . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('dokumen_pasien', $fileName, 'public');
            $data['dokumen_path'] = $filePath;
        }

        Pasien::create($data);

        return redirect()->route('pasien.index')
            ->with('success', 'Data pasien berhasil ditambahkan');
    }

    public function show(Pasien $pasien)
    {
        return view('pasien.show', compact('pasien'));
    }

    public function edit(Pasien $pasien)
    {
        return view('pasien.edit', compact('pasien'));
    }

    public function update(Request $request, Pasien $pasien)
    {
        $request->validate([
            'no_mr' => 'required|max:20|unique:tbl_pasien,no_mr,' . $pasien->id,
            'nama_pasien' => 'required|max:100',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|max:200',
            'tgl_daftar' => 'required|date',
            'no_bpjs' => 'nullable|max:30',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ], [
            'no_mr.required' => 'Nomor MR wajib diisi',
            'no_mr.unique' => 'Nomor MR sudah digunakan',
            'nama_pasien.required' => 'Nama pasien wajib diisi',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
            'alamat.required' => 'Alamat wajib diisi',
            'tgl_daftar.required' => 'Tanggal daftar wajib diisi',
            'dokumen.mimes' => 'File dokumen harus berformat PDF, JPG, JPEG, atau PNG',
            'dokumen.max' => 'Ukuran file dokumen maksimal 2MB',
        ]);

        $data = $request->except('dokumen');

        // Handle file upload
        if ($request->hasFile('dokumen')) {
            // Delete old file if exists
            if ($pasien->dokumen_path && Storage::disk('public')->exists($pasien->dokumen_path)) {
                Storage::disk('public')->delete($pasien->dokumen_path);
            }

            $file = $request->file('dokumen');
            $fileName = 'pasien_' . $request->no_mr . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('dokumen_pasien', $fileName, 'public');
            $data['dokumen_path'] = $filePath;
        }

        $pasien->update($data);

        return redirect()->route('pasien.index')
            ->with('success', 'Data pasien berhasil diperbarui');
    }

    public function destroy(Pasien $pasien)
    {
        // Delete file if exists
        if ($pasien->dokumen_path && Storage::disk('public')->exists($pasien->dokumen_path)) {
            Storage::disk('public')->delete($pasien->dokumen_path);
        }

        $pasien->delete();

        return redirect()->route('pasien.index')
            ->with('success', 'Data pasien berhasil dihapus');
    }

    public function downloadDokumen(Pasien $pasien)
    {
        if (!$pasien->dokumen_path || !Storage::disk('public')->exists($pasien->dokumen_path)) {
            return redirect()->back()->with('error', 'File dokumen tidak ditemukan');
        }

        return Storage::disk('public')->download($pasien->dokumen_path);
    }
}