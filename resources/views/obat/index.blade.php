@extends('adminlte::page')

@section('title', 'Manajemen Obat')

@section('content_header')
    <h1>Manajemen Obat</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">Daftar Obat</h3>
            </div>
            <div class="col-md-6 text-right">
                <a href="{{ route('obat.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Obat
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Search Form -->
        <form action="{{ route('obat.search') }}" method="GET" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="Cari nama/kode obat..." value="{{ request('q') }}">
                </div>
                <div class="col-md-3">
                    <select name="kategori" class="form-control">
                        <option value="">Semua Kategori</option>
                        <option value="Antibiotik" {{ request('kategori') == 'Antibiotik' ? 'selected' : '' }}>Antibiotik</option>
                        <option value="Analgesik" {{ request('kategori') == 'Analgesik' ? 'selected' : '' }}>Analgesik</option>
                        <option value="Antipiretik" {{ request('kategori') == 'Antipiretik' ? 'selected' : '' }}>Antipiretik</option>
                        <option value="Vitamin" {{ request('kategori') == 'Vitamin' ? 'selected' : '' }}>Vitamin</option>
                        <option value="Suplemen" {{ request('kategori') == 'Suplemen' ? 'selected' : '' }}>Suplemen</option>
                        <option value="Obat Luar" {{ request('kategori') == 'Obat Luar' ? 'selected' : '' }}>Obat Luar</option>
                        <option value="Lainnya" {{ request('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Aktif" {{ request('status') == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary btn-block">
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
                        <th width="12%">Kode Obat</th>
                        <th width="20%">Nama Obat</th>
                        <th width="10%">Kategori</th>
                        <th width="8%">Satuan</th>
                        <th width="8%">Stok</th>
                        <th width="12%">Harga</th>
                        <th width="8%">Status</th>
                        <th width="17%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($obat as $index => $item)
                        <tr>
                            <td>{{ $index + 1 + ($obat->currentPage() - 1) * $obat->perPage() }}</td>
                            <td>
                                <strong>{{ $item->kode_obat }}</strong>
                            </td>
                            <td>
                                {{ $item->nama_obat }}
                                <br><small class="text-muted">{{ $item->dosis }}</small>
                            </td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>
                                <span class="badge {{ $item->stok_badge }}">
                                    {{ $item->stok }}
                                </span>
                                <br><small class="text-muted">{{ $item->stok_status }}</small>
                            </td>
                            <td>{{ $item->harga_formatted }}</td>
                            <td>
                                <span class="badge {{ $item->status_badge }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('obat.show', $item->id) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('obat.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-primary" title="Update Stok" 
                                            onclick="updateStok({{ $item->id }}, '{{ $item->nama_obat }}')">
                                        <i class="fas fa-boxes"></i>
                                    </button>
                                    <form action="{{ route('obat.destroy', $item->id) }}" method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat ini?')">
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
                            <td colspan="9" class="text-center">Tidak ada data obat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-3">
            {{ $obat->links() }}
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $obat->where('status', 'Aktif')->count() }}</h3>
                <p>Obat Aktif</p>
            </div>
            <div class="icon">
                <i class="fas fa-pills"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $obat->where('stok_status', 'Tersedia')->count() }}</h3>
                <p>Stok Tersedia</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $obat->where('stok_status', 'Kritis')->count() }}</h3>
                <p>Stok Kritis</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $obat->where('stok_status', 'Habis')->count() }}</h3>
                <p>Stok Habis</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Update Stok Modal -->
<div class="modal fade" id="stokModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Stok Obat</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="stokForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Obat</label>
                        <input type="text" id="namaObat" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe Update</label>
                        <select name="tipe" class="form-control" required>
                            <option value="tambah">Tambah Stok</option>
                            <option value="kurang">Kurang Stok</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Obat Index loaded!');
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        function updateStok(obatId, namaObat) {
            const form = document.getElementById('stokForm');
            const namaObatInput = document.getElementById('namaObat');
            
            form.action = `/obat/${obatId}/stok`;
            namaObatInput.value = namaObat;
            
            $('#stokModal').modal('show');
        }
    </script>
@stop
