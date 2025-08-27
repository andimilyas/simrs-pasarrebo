@extends('adminlte::page')

@section('title', 'Pengaturan Sistem')

@section('content_header')
    <h1>Pengaturan Sistem</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Umum</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label>Nama Rumah Sakit</label>
                        <input type="text" class="form-control" value="SIMRS Pasar Rebo" placeholder="Nama Rumah Sakit">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea class="form-control" rows="3" placeholder="Alamat Rumah Sakit">Jl. Raya Pasar Rebo No. 123, Jakarta Timur</textarea>
                    </div>
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" class="form-control" value="021-1234567" placeholder="Nomor Telepon">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="info@simrs-pasarrebo.com" placeholder="Email">
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pengaturan Sistem</h3>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <label>Zona Waktu</label>
                        <select class="form-control">
                            <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Bahasa</label>
                        <select class="form-control">
                            <option value="id" selected>Bahasa Indonesia</option>
                            <option value="en">English</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Format Tanggal</label>
                        <select class="form-control">
                            <option value="d/m/Y" selected>DD/MM/YYYY</option>
                            <option value="Y-m-d">YYYY-MM-DD</option>
                            <option value="m/d/Y">MM/DD/YYYY</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Backup Otomatis</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="autoBackup" checked>
                            <label class="custom-control-label" for="autoBackup">Aktifkan backup otomatis</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Log Sistem</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Level</th>
                                <th>Pesan</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-08-27 10:30:15</td>
                                <td><span class="badge badge-info">INFO</span></td>
                                <td>Sistem berhasil di-restart</td>
                                <td>Admin SIMRS</td>
                            </tr>
                            <tr>
                                <td>2025-08-27 10:25:30</td>
                                <td><span class="badge badge-warning">WARNING</span></td>
                                <td>Backup database belum dilakukan</td>
                                <td>System</td>
                            </tr>
                            <tr>
                                <td>2025-08-27 10:20:45</td>
                                <td><span class="badge badge-success">SUCCESS</span></td>
                                <td>User baru berhasil ditambahkan</td>
                                <td>Admin SIMRS</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
        console.log('Settings loaded!');
    </script>
@stop
