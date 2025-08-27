<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailResep extends Model
{
    use HasFactory;

    protected $table = 'detail_resep';
    
    protected $fillable = [
        'resep_id',
        'obat_id',
        'jumlah',
        'aturan_pakai',
        'harga_satuan',
        'subtotal',
        'catatan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }

    // Accessors
    public function getSubtotalFormattedAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getHargaSatuanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    // Methods
    public function calculateSubtotal()
    {
        $this->subtotal = $this->jumlah * $this->harga_satuan;
        $this->save();
        return $this->subtotal;
    }

    public function updateHargaSatuan($hargaBaru)
    {
        $this->harga_satuan = $hargaBaru;
        $this->calculateSubtotal();
    }

    public function updateJumlah($jumlahBaru)
    {
        $this->jumlah = $jumlahBaru;
        $this->calculateSubtotal();
    }
}
