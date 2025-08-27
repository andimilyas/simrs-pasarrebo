@extends('adminlte::page')

@section('title', 'Detail Pasien')

@section('content_header')
    <h1>Detail Pasien</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Informasi Lengkap Pasien</h3>
        <div class="card-tools">
            <a href="{{ route('pasien.edit', $pasien->id) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('pasien.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="35%"><strong>ID Pasien:</strong></td>
                        <td>{{ $pasien->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor MR:</strong></td>
                        <td><span class="badge badge-primary">{{ $pasien->no_mr }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Nama Lengkap:</strong></td>
                        <td>{{ $pasien->nama_pasien }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Lahir:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Usia:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->age }} tahun</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Kelamin:</strong></td>
                        <td>
                            @if($pasien->jenis_kelamin == 'L')
                                <span class="badge badge-info">Laki-laki</span>
                            @else
                                <span class="badge badge-pink">Perempuan</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="35%"><strong>Alamat:</strong></td>
                        <td>{{ $pasien->alamat }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tanggal Daftar:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_daftar)->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Lama Terdaftar:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_daftar)->diffForHumans() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Nomor BPJS:</strong></td>
                        <td>
                            @if($pasien->no_bpjs)
                                <span class="badge badge-success">{{ $pasien->no_bpjs }}</span>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge badge-success">Aktif</span></td>
                    </tr>
                    <tr>
                        <td><strong>Dokumen:</strong></td>
                        <td>
                            @if($pasien->dokumen_path)
                                <a href="{{ route('pasien.download-dokumen', $pasien->id) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                <span class="text-muted">Tidak ada dokumen</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Riwayat Sistem</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Dibuat pada:</strong> {{ $pasien->created_at->format('d/m/Y H:i:s') }}</p>
                <p><strong>Terakhir update:</strong> {{ $pasien->updated_at->format('d/m/Y H:i:s') }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Dibuat oleh:</strong> <span class="text-muted">System</span></p>
                <p><strong>Terakhir update oleh:</strong> <span class="text-muted">System</span></p>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Pasien Show loaded!');
    </script>
@stop
