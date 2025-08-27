@extends('adminlte::page')

@section('title', 'Tambah Pasien Baru')

@section('content_header')
    <h1>Tambah Pasien Baru</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Data Pasien</h3>
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

        <form action="{{ route('pasien.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_mr">Nomor MR <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('no_mr') is-invalid @enderror" 
                               id="no_mr" name="no_mr" value="{{ old('no_mr') }}" 
                               placeholder="Masukkan nomor MR" required>
                        @error('no_mr')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nama_pasien">Nama Pasien <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_pasien') is-invalid @enderror" 
                               id="nama_pasien" name="nama_pasien" value="{{ old('nama_pasien') }}" 
                               placeholder="Masukkan nama lengkap pasien" required>
                        @error('nama_pasien')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tgl_lahir') is-invalid @enderror" 
                               id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
                        @error('tgl_lahir')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                        <select class="form-control @error('jenis_kelamin') is-invalid @enderror" 
                                id="jenis_kelamin" name="jenis_kelamin" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
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
                                  placeholder="Masukkan alamat lengkap pasien" required>{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tgl_daftar">Tanggal Daftar <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tgl_daftar') is-invalid @enderror" 
                               id="tgl_daftar" name="tgl_daftar" 
                               value="{{ old('tgl_daftar', date('Y-m-d')) }}" required>
                        @error('tgl_daftar')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="no_bpjs">Nomor BPJS</label>
                        <input type="text" class="form-control @error('no_bpjs') is-invalid @enderror" 
                               id="no_bpjs" name="no_bpjs" value="{{ old('no_bpjs') }}" 
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
                            <label class="custom-file-label" for="dokumen">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">
                            Format yang didukung: PDF, JPG, JPEG, PNG. Maksimal 2MB.
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
                            <i class="fas fa-save"></i> Simpan Data
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
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        console.log('Pasien Create loaded!');
        
        // Custom file input
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
        
        // Set default date for tgl_daftar
        document.getElementById('tgl_daftar').value = new Date().toISOString().split('T')[0];
    </script>
@stop
