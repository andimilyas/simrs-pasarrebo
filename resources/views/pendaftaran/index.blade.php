@extends('adminlte::page')

@section('title', 'Daftar Pendaftaran')

@section('content_header')
    <h1>Daftar Pendaftaran</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-6">
                <h3 class="card-title">
                    @if(auth()->user()->role === 'admin')
                        Semua Pendaftaran Hari Ini
                    @elseif(auth()->user()->role === 'dokter')
                        Daftar Pasien Saya
                    @else
                        Pendaftaran Saya
                    @endif
                </h3>
            </div>
            <div class="col-md-6 text-right">
                @if(auth()->user()->role === 'petugas')
                    <a href="{{ route('pendaftaran.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Pendaftaran
                    </a>
                @endif
                <a href="{{ route('pendaftaran.antrian') }}" class="btn btn-info">
                    <i class="fas fa-list-ol"></i> Lihat Antrian
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body">
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
                        <th width="12%">No Pendaftaran</th>
                        <th width="15%">Nama Pasien</th>
                        <th width="10%">Poli</th>
                        <th width="10%">Dokter</th>
                        <th width="8%">Jam</th>
                        <th width="8%">Status</th>
                        <th width="8%">Prioritas</th>
                        <th width="14%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendaftaran as $index => $reg)
                        <tr>
                            <td>{{ $index + 1 + ($pendaftaran->currentPage() - 1) * $pendaftaran->perPage() }}</td>
                            <td>
                                <strong>{{ $reg->no_pendaftaran }}</strong>
                                <br><small class="text-muted">{{ $reg->jenis_pendaftaran }}</small>
                            </td>
                            <td>
                                {{ $reg->pasien->nama_pasien }}
                                <br><small class="text-muted">{{ $reg->pasien->no_mr }}</small>
                            </td>
                            <td>{{ $reg->poli->nama_poli }}</td>
                            <td>
                                @if($reg->dokter)
                                    {{ $reg->dokter->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($reg->jam_pendaftaran)->format('H:i') }}</td>
                            <td>
                                <span class="badge {{ $reg->status_badge }}">
                                    {{ $reg->status_pendaftaran }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $reg->prioritas_badge }}">
                                    {{ $reg->prioritas }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('pendaftaran.show', $reg->id) }}" class="btn btn-sm btn-info" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(auth()->user()->role === 'petugas' && $reg->status_pendaftaran === 'Terdaftar')
                                        <a href="{{ route('pendaftaran.edit', $reg->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->role === 'dokter' && in_array($reg->status_pendaftaran, ['Terdaftar', 'Dalam Antrian']))
                                        <button type="button" class="btn btn-sm btn-primary" title="Update Status" 
                                                onclick="updateStatus({{ $reg->id }}, '{{ $reg->status_pendaftaran === 'Terdaftar' ? 'Dalam Antrian' : 'Sedang Diperiksa' }}')">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    @endif
                                    
                                    @if($reg->status_pendaftaran === 'Sedang Diperiksa' && auth()->user()->role === 'dokter')
                                        <button type="button" class="btn btn-sm btn-success" title="Selesai" 
                                                onclick="updateStatus({{ $reg->id }}, 'Selesai')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    
                                    @if($reg->status_pendaftaran === 'Terdaftar' && auth()->user()->role === 'petugas')
                                        <form action="{{ route('pendaftaran.destroy', $reg->id) }}" method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Batalkan">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data pendaftaran</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pendaftaran->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $pendaftaran->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $pendaftaran->where('status_pendaftaran', 'Terdaftar')->count() }}</h3>
                <p>Terdaftar</p>
            </div>
            <div class="icon">
                <i class="fas fa-user-plus"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendaftaran->where('status_pendaftaran', 'Dalam Antrian')->count() }}</h3>
                <p>Dalam Antrian</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $pendaftaran->where('status_pendaftaran', 'Sedang Diperiksa')->count() }}</h3>
                <p>Sedang Diperiksa</p>
            </div>
            <div class="icon">
                <i class="fas fa-stethoscope"></i>
            </div>
        </div>
    </div>
    
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $pendaftaran->where('status_pendaftaran', 'Selesai')->count() }}</h3>
                <p>Selesai</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Pendaftaran</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="statusForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status Baru</label>
                        <select name="status" class="form-control" required>
                            <option value="Terdaftar">Terdaftar</option>
                            <option value="Dalam Antrian">Dalam Antrian</option>
                            <option value="Sedang Diperiksa">Sedang Diperiksa</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Batal">Batal</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
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
        console.log('Pendaftaran Index loaded!');
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        function updateStatus(pendaftaranId, newStatus) {
            const form = document.getElementById('statusForm');
            const statusSelect = form.querySelector('select[name="status"]');
            
            form.action = `/pendaftaran/${pendaftaranId}/status`;
            statusSelect.value = newStatus;
            
            $('#statusModal').modal('show');
        }
    </script>
@stop
