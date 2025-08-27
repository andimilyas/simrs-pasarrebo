<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Daftar extends Model
{
    use HasFactory;

    protected $table = 'tbl_daftar';
    
    protected $fillable = [
        'no_reg',
        'no_mr',
        'kode_poli',
        'nama_poli',
        'tgl_kunjungan',
        'dokter_id',
        'cara_bayar'
    ];

    protected $casts = [
        'tgl_kunjungan' => 'date',
    ];

    // Relationships
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class, 'no_mr', 'no_mr');
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_id');
    }

    public function reseps(): HasMany
    {
        return $this->hasMany(Resep::class, 'no_reg', 'no_reg');
    }

    public function activityTasks(): HasMany
    {
        return $this->hasMany(ActivityTask::class, 'no_reg', 'no_reg');
    }

    public function billing(): HasOne
    {
        return $this->hasOne(Billing::class, 'no_reg', 'no_reg');
    }

    // Accessor untuk cara bayar readable
    public function getCaraBayarTextAttribute()
    {
        return $this->cara_bayar === 'jkn' ? 'JKN/BPJS' : 'Non JKN';
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tgl_kunjungan', $tanggal);
    }
}