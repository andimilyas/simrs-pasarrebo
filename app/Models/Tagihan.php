<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    
    protected $fillable = [
        'no_tagihan',
        'pendaftaran_id',
        'pasien_id',
        'total_biaya',
        'biaya_pendaftaran',
        'biaya_poli',
        'biaya_dokter',
        'biaya_tambahan',
        'diskon',
        'ppn',
        'total_bayar',
        'status_pembayaran', // Belum Bayar, Sebagian, Lunas
        'metode_pembayaran', // Tunai, Transfer, Kartu Kredit
        'tanggal_bayar',
        'catatan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_biaya' => 'decimal:2',
        'biaya_pendaftaran' => 'decimal:2',
        'biaya_poli' => 'decimal:2',
        'biaya_dokter' => 'decimal:2',
        'biaya_tambahan' => 'decimal:2',
        'diskon' => 'decimal:2',
        'ppn' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'tanggal_bayar' => 'datetime',
    ];

    // Relationships
    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    // Scopes
    public function scopeBelumLunas($query)
    {
        return $query->where('status_pembayaran', '!=', 'Lunas');
    }

    public function scopeLunas($query)
    {
        return $query->where('status_pembayaran', 'Lunas');
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'Belum Bayar' => 'badge-danger',
            'Sebagian' => 'badge-warning',
            'Lunas' => 'badge-success',
        ];

        return $statuses[$this->status_pembayaran] ?? 'badge-secondary';
    }

    public function getSisaBayarAttribute()
    {
        return $this->total_bayar - $this->pembayaran->sum('jumlah_bayar');
    }

    public function getPersentaseBayarAttribute()
    {
        if ($this->total_bayar == 0) return 0;
        return round(($this->pembayaran->sum('jumlah_bayar') / $this->total_bayar) * 100, 2);
    }

    // Methods
    public function generateNoTagihan()
    {
        $today = now()->format('Ymd');
        $lastTagihan = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTagihan) {
            $lastNumber = (int) substr($lastTagihan->no_tagihan, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'INV-' . $today . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotal()
    {
        $this->total_biaya = $this->biaya_pendaftaran + $this->biaya_poli + $this->biaya_dokter + $this->biaya_tambahan;
        
        // Hitung PPN (11%)
        $this->ppn = $this->total_biaya * 0.11;
        
        // Hitung total setelah diskon dan PPN
        $this->total_bayar = $this->total_biaya + $this->ppn - $this->diskon;
        
        $this->save();
    }

    public function updateStatusPembayaran()
    {
        $totalBayar = $this->pembayaran->sum('jumlah_bayar');
        
        if ($totalBayar >= $this->total_bayar) {
            $this->status_pembayaran = 'Lunas';
            $this->tanggal_bayar = now();
        } elseif ($totalBayar > 0) {
            $this->status_pembayaran = 'Sebagian';
        } else {
            $this->status_pembayaran = 'Belum Bayar';
        }
        
        $this->save();
    }
}
