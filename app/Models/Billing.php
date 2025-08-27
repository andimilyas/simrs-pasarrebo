<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'tbl_billing';
    
    protected $fillable = [
        'no_reg',
        'total_obat',
        'total_jasa',
        'total_tagihan',
        'status_pembayaran',
        'tgl_billing'
    ];

    protected $casts = [
        'total_obat' => 'decimal:2',
        'total_jasa' => 'decimal:2',
        'total_tagihan' => 'decimal:2',
        'tgl_billing' => 'date',
    ];

    // Relationships
    public function daftar(): BelongsTo
    {
        return $this->belongsTo(Daftar::class, 'no_reg', 'no_reg');
    }

    // Accessor untuk status pembayaran readable
    public function getStatusPembayaranTextAttribute()
    {
        $statuses = [
            'unpaid' => 'Belum Dibayar',
            'paid' => 'Lunas',
            'partial' => 'Dibayar Sebagian'
        ];

        return $statuses[$this->status_pembayaran] ?? $this->status_pembayaran;
    }

    // Accessor untuk format currency
    public function getTotalObatFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_obat, 0, ',', '.');
    }

    public function getTotalJasaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_jasa, 0, ',', '.');
    }

    public function getTotalTagihanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->total_tagihan, 0, ',', '.');
    }

    // Scope untuk status pembayaran
    public function scopeByStatusPembayaran($query, $status)
    {
        return $query->where('status_pembayaran', $status);
    }

    // Scope untuk periode tanggal
    public function scopeByPeriode($query, $dari, $sampai)
    {
        return $query->whereBetween('tgl_billing', [$dari, $sampai]);
    }
}