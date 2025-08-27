@extends('adminlte::page')

@section('title', 'Daftar Pasien')

@section('content_header')
    <h1>Daftar Pasien</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Data Pasien</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('pasien.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Pasien
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('pasien.index') }}" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, NIK, atau alamat..." value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-outline-secondary">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">No MR</th>
                        <th width="20%">Nama Pasien</th>
                        <th width="10%">Tgl Lahir</th>
                        <th width="8%">JK</th>
                        <th width="20%">Alamat</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pasiens as $index => $pasien)
                        <tr>
                            <td>{{ $index + 1 + ($pasiens->currentPage() - 1) * $pasiens->perPage() }}</td>
                            <td>
                                <strong>{{ $pasien->no_mr }}</strong>
                                @if($pasien->no_bpjs)
                                    <br><small class="text-muted">BPJS: {{ $pasien->no_bpjs }}</small>
                                @endif
                            </td>
                            <td>{{ $pasien->nama_pasien }}</td>
                            <td>{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->format('d/m/Y') }}</td>
                            <td>
                                @if($pasien->jenis_kelamin == 'L')
                                    <span class="badge badge-info">Laki-laki</span>
                                @else
                                    <span class="badge badge-pink">Perempuan</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($pasien->alamat, 50) }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pasien.show', $pasien->id) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('pasien.edit', $pasien->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($pasien->dokumen_path)
                                        <a href="{{ route('pasien.download-dokumen', $pasien->id) }}" class="btn btn-sm btn-success" title="Download Dokumen">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    <form action="{{ route('pasien.destroy', $pasien->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pasien ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data pasien</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $pasiens->links() }}
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $pasiens->total() }}</h3>
                <p>Total Pasien</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $pasiens->where('jenis_kelamin', 'L')->count() }}</h3>
                <p>Pasien Laki-laki</p>
            </div>
            <div class="icon">
                <i class="fas fa-male"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pasiens->where('jenis_kelamin', 'P')->count() }}</h3>
                <p>Pasien Perempuan</p>
            </div>
            <div class="icon">
                <i class="fas fa-female"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $pasiens->where('tgl_daftar', \Carbon\Carbon::today())->count() }}</h3>
                <p>Pasien Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar-day"></i>
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
        console.log('Pasien Index loaded!');
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
@stop
