<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';
    
    protected $fillable = [
        'kode_obat',
        'nama_obat',
        'kategori',
        'satuan', // Tablet, Kapsul, Syrup, Injeksi, dll
        'dosis',
        'indikasi',
        'kontraindikasi',
        'efek_samping',
        'harga',
        'stok',
        'minimal_stok',
        'status', // Aktif, Nonaktif
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok' => 'integer',
        'minimal_stok' => 'integer',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function detailResep()
    {
        return $this->hasMany(DetailResep::class);
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    public function scopeStokTersedia($query)
    {
        return $query->where('stok', '>', 0);
    }

    public function scopeStokHabis($query)
    {
        return $query->where('stok', '<=', 0);
    }

    public function scopeStokKritis($query)
    {
        return $query->whereRaw('stok <= minimal_stok');
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        return $this->status === 'Aktif' ? 'badge-success' : 'badge-danger';
    }

    public function getStokBadgeAttribute()
    {
        if ($this->stok <= 0) {
            return 'badge-danger';
        } elseif ($this->stok <= $this->minimal_stok) {
            return 'badge-warning';
        } else {
            return 'badge-success';
        }
    }

    public function getStokStatusAttribute()
    {
        if ($this->stok <= 0) {
            return 'Habis';
        } elseif ($this->stok <= $this->minimal_stok) {
            return 'Kritis';
        } else {
            return 'Tersedia';
        }
    }

    public function getHargaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    // Methods
    public function updateStok($jumlah, $tipe = 'kurang')
    {
        if ($tipe === 'kurang') {
            $this->stok -= $jumlah;
        } else {
            $this->stok += $jumlah;
        }
        
        $this->save();
    }

    public function isStokCukup($jumlah)
    {
        return $this->stok >= $jumlah;
    }

    public function generateKodeObat()
    {
        $kategori = strtoupper(substr($this->kategori, 0, 3));
        $lastObat = static::where('kategori', $this->kategori)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastObat) {
            $lastNumber = (int) substr($lastObat->kode_obat, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $kategori . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}