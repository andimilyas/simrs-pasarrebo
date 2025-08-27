<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    use HasFactory;

    protected $table = 'poli';
    
    protected $fillable = [
        'nama_poli',
        'kode_poli',
        'deskripsi',
        'biaya',
        'kapasitas_harian',
        'jam_buka',
        'jam_tutup',
        'status', // Aktif, Nonaktif
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'jam_buka' => 'datetime',
        'jam_tutup' => 'datetime',
        'biaya' => 'decimal:2',
    ];

    // Relationships
    public function dokter()
    {
        return $this->hasMany(User::class)->where('role', 'dokter');
    }

    public function pendaftaran()
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'Aktif' ? 'badge-success' : 'badge-danger';
    }

    public function getKapasitasTersisaAttribute()
    {
        $terpakai = $this->pendaftaran()
            ->whereDate('tanggal_pendaftaran', today())
            ->count();
        
        return max(0, $this->kapasitas_harian - $terpakai);
    }

    public function getJadwalAttribute()
    {
        return $this->jam_buka->format('H:i') . ' - ' . $this->jam_tutup->format('H:i');
    }
}
