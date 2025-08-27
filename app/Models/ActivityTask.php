<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityTask extends Model
{
    use HasFactory;

    protected $table = 'tbl_activity_task';
    
    protected $fillable = [
        'no_reg',
        'activity',
        'status',
        'tgl_mulai',
        'tgl_selesai',
        'petugas'
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
    ];

    // Relationships
    public function daftar(): BelongsTo
    {
        return $this->belongsTo(Daftar::class, 'no_reg', 'no_reg');
    }

    // Accessor untuk status readable
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Dikerjakan',
            'done' => 'Selesai'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    // Accessor untuk durasi (dalam menit)
    public function getDurasiAttribute()
    {
        if (!$this->tgl_selesai || !$this->tgl_mulai) {
            return null;
        }

        return $this->tgl_mulai->diffInMinutes($this->tgl_selesai);
    }

    // Scope untuk status tertentu
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk hari ini
    public function scopeHariIni($query)
    {
        return $query->whereDate('tgl_mulai', today());
    }
}