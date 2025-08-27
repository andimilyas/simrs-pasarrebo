<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dokter extends Model
{
    use HasFactory;

    protected $table = 'tbl_dokter';
    
    protected $fillable = [
        'kode_dokter',
        'nama_dokter',
        'spesialis'
    ];

    // Relationships
    public function daftars(): HasMany
    {
        return $this->hasMany(Daftar::class, 'dokter_id');
    }

    // Scope untuk filter berdasarkan spesialis
    public function scopeBySpesialis($query, $spesialis)
    {
        return $query->where('spesialis', $spesialis);
    }
}