<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasien extends Model
{
    use HasFactory;

    protected $table = 'tbl_pasien';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'no_mr',
        'nama_pasien',
        'tgl_lahir',
        'jenis_kelamin',
        'alamat',
        'tgl_daftar',
        'no_bpjs',
        'dokumen_path'
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_daftar' => 'date',
    ];

    // Relationships
    public function daftars(): HasMany
    {
        return $this->hasMany(Daftar::class, 'no_mr', 'no_mr');
    }

    // Accessor untuk mendapatkan umur
    public function getUmurAttribute()
    {
        return $this->tgl_lahir ? $this->tgl_lahir->age : null;
    }

    // Accessor untuk jenis kelamin readable
    public function getJenisKelaminTextAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}