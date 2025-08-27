@extends('adminlte::page')

@section('title', 'Edit Data Pasien')

@section('content_header')
    <h1>Edit Data Pasien</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Data Pasien</h3>
        <div class="card-tools">
            <a href="{{ route('pasien.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pasien.update', $pasien->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_mr">Nomor MR <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_mr') is-invalid @enderror" 
                               id="no_mr" name="no_mr" value="{{ old('no_mr', $pasien->no_mr) }}" 
                               placeholder="Masukkan nomor MR" required>
                        @error('no_mr')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nama_pasien">Nama Pasien <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pasien') is-invalid @enderror" 
                               id="nama_pasien" name="nama_pasien" value="{{ old('nama_pasien', $pasien->nama_pasien) }}" 
                               placeholder="Masukkan nama lengkap pasien" required>
                        @error('nama_pasien')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror" 
                               id="tgl_lahir" name="tgl_lahir" 
                               value="{{ old('tgl_lahir', $pasien->tgl_lahir) }}" required>
                        @error('tgl_lahir')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" 
                                id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $pasien->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="alamat">Alamat <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                  id="alamat" name="alamat" rows="3" 
                                  placeholder="Masukkan alamat lengkap pasien" required>{{ old('alamat', $pasien->alamat) }}</textarea>
                        @error('alamat')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tgl_daftar">Tanggal Daftar <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tgl_daftar') is-invalid @enderror" 
                               id="tgl_daftar" name="tgl_daftar" 
                               value="{{ old('tgl_daftar', $pasien->tgl_daftar) }}" required>
                        @error('tgl_daftar')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="no_bpjs">Nomor BPJS</label>
                        <input type="text" class="form-control @error('no_bpjs') is-invalid @enderror" 
                               id="no_bpjs" name="no_bpjs" value="{{ old('no_bpjs', $pasien->no_bpjs) }}" 
                               placeholder="Masukkan nomor BPJS (opsional)">
                        @error('no_bpjs')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="dokumen">Dokumen Pendukung</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('dokumen') is-invalid @enderror" 
                                   id="dokumen" name="dokumen" accept=".pdf,.jpg,.jpeg,.png">
                            <label class="custom-file-label" for="dokumen">
                                @if($pasien->dokumen_path)
                                    {{ basename($pasien->dokumen_path) }}
                                @else
                                    Pilih file...
                                @endif
                            </label>
                        </div>
                        <small class="form-text text-muted">
                            Format yang didukung: PDF, JPG, JPEG, PNG. Maksimal 2MB.
                            @if($pasien->dokumen_path)
                                <br><strong>File saat ini:</strong> 
                                <a href="{{ route('pasien.download-dokumen', $pasien->id) }}" target="_blank">
                                    {{ basename($pasien->dokumen_path) }}
                                </a>
                            @endif
                        </small>
                        @error('dokumen')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Data
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <a href="{{ route('pasien.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Patient Info Card -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">Informasi Pasien</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>ID Pasien:</strong></td>
                        <td>{{ $pasien->id }}</td>
                    </tr>
                    <tr>
                        <td><strong>Dibuat:</strong></td>
                        <td>{{ $pasien->created_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Terakhir Update:</strong></td>
                        <td>{{ $pasien->updated_at->format('d/m/Y H:i:s') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%"><strong>Status:</strong></td>
                        <td><span class="badge badge-success">Aktif</span></td>
                    </tr>
                    <tr>
                        <td><strong>Usia:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_lahir)->age }} tahun</td>
                    </tr>
                    <tr>
                        <td><strong>Lama Terdaftar:</strong></td>
                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_daftar)->diffForHumans() }}</td>
                    </tr>
                </table>
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
        console.log('Pasien Edit loaded!');
        
        // Custom file input
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    </script>
@stop
