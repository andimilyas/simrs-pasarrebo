<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasFactory;

    protected $table = 'resep';
    
    protected $fillable = [
        'no_resep',
        'pendaftaran_id',
        'pasien_id',
        'dokter_id',
        'tanggal_resep',
        'diagnosa',
        'catatan_dokter',
        'status_resep', // Draft, Aktif, Selesai, Batal
        'total_harga',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_resep' => 'date',
        'total_harga' => 'decimal:2',
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

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

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

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'reference_id')->where('reference_type', 'resep');
    }

    // Scopes
    public function scopeByDokter($query, $dokterId)
    {
        return $query->where('dokter_id', $dokterId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_resep', $status);
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_resep', today());
    }

    public function scopeByPendaftaran($query, $pendaftaranId)
    {
        return $query->where('pendaftaran_id', $pendaftaranId);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'Draft' => 'badge-secondary',
            'Aktif' => 'badge-info',
            'Selesai' => 'badge-success',
            'Batal' => 'badge-danger',
        ];

        return $statuses[$this->status_resep] ?? 'badge-secondary';
    }

    public function getTotalObatAttribute()
    {
        return $this->detailResep->count();
    }

    public function getTotalQuantityAttribute()
    {
        return $this->detailResep->sum('jumlah');
    }

    // Methods
    public function generateNoResep()
    {
        $today = now()->format('Ymd');
        $lastResep = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastResep) {
            $lastNumber = (int) substr($lastResep->no_resep, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'RSP-' . $today . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotalHarga()
    {
        $total = $this->detailResep->sum(function ($detail) {
            return $detail->jumlah * $detail->harga_satuan;
        });

        $this->update(['total_harga' => $total]);
        return $total;
    }

    public function updateStatus($status, $userId = null)
    {
        $this->update([
            'status_resep' => $status,
            'updated_by' => $userId ?? auth()->id(),
        ]);

        // Log activity
        $this->logActivity('Status resep diubah menjadi: ' . $status, $userId);
    }

    public function logActivity($description, $userId = null)
    {
        ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'activity_type' => 'resep',
            'description' => $description,
            'reference_type' => 'resep',
            'reference_id' => $this->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function isEditable()
    {
        return in_array($this->status_resep, ['Draft', 'Aktif']);
    }

    public function isCompleted()
    {
        return $this->status_resep === 'Selesai';
    }
}