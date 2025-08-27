<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftaran';
    
    protected $fillable = [
        'no_pendaftaran',
        'pasien_id',
        'dokter_id',
        'poli_id',
        'tanggal_pendaftaran',
        'jam_pendaftaran',
        'jenis_pendaftaran', // Umum, BPJS, Asuransi
        'status_pendaftaran', // Terdaftar, Dalam Antrian, Sedang Diperiksa, Selesai, Batal
        'keluhan',
        'prioritas', // Normal, Urgent, Emergency
        'estimasi_waktu',
        'catatan_petugas',
        'created_by', // ID user yang membuat pendaftaran
        'updated_by',
    ];

    protected $casts = [
        'tanggal_pendaftaran' => 'date',
        'jam_pendaftaran' => 'datetime',
        'estimasi_waktu' => 'datetime',
    ];

    // Relationships
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'dokter_id');
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function pemeriksaan()
    {
        return $this->hasOne(Pemeriksaan::class);
    }

    public function resep()
    {
        return $this->hasOne(Resep::class);
    }

    public function tagihan()
    {
        return $this->hasOne(Tagihan::class);
    }

    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'reference_id')->where('reference_type', 'pendaftaran');
    }

    // Scopes
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_pendaftaran', today());
    }

    public function scopeByPoli($query, $poliId)
    {
        return $query->where('poli_id', $poliId);
    }

    public function scopeByDokter($query, $dokterId)
    {
        return $query->where('dokter_id', $dokterId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pendaftaran', $status);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $statuses = [
            'Terdaftar' => 'badge-info',
            'Dalam Antrian' => 'badge-warning',
            'Sedang Diperiksa' => 'badge-primary',
            'Selesai' => 'badge-success',
            'Batal' => 'badge-danger',
        ];

        return $statuses[$this->status_pendaftaran] ?? 'badge-secondary';
    }

    public function getPrioritasBadgeAttribute()
    {
        $prioritas = [
            'Normal' => 'badge-success',
            'Urgent' => 'badge-warning',
            'Emergency' => 'badge-danger',
        ];

        return $prioritas[$this->prioritas] ?? 'badge-secondary';
    }

    public function getWaktuTungguAttribute()
    {
        if ($this->jam_pendaftaran && $this->estimasi_waktu) {
            $now = Carbon::now();
            $estimasi = Carbon::parse($this->estimasi_waktu);
            return $now->diffInMinutes($estimasi);
        }
        return null;
    }

    // Methods
    public function updateStatus($status, $userId = null)
    {
        $this->update([
            'status_pendaftaran' => $status,
            'updated_by' => $userId ?? auth()->id(),
        ]);

        // Log activity
        $this->logActivity('Status pendaftaran diubah menjadi: ' . $status, $userId);
    }

    public function logActivity($description, $userId = null)
    {
        ActivityLog::create([
            'user_id' => $userId ?? auth()->id(),
            'activity_type' => 'pendaftaran',
            'description' => $description,
            'reference_type' => 'pendaftaran',
            'reference_id' => $this->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function generateNoPendaftaran()
    {
        $today = now()->format('Ymd');
        $lastPendaftaran = static::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastPendaftaran) {
            $lastNumber = (int) substr($lastPendaftaran->no_pendaftaran, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return 'REG-' . $today . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function calculateBiaya()
    {
        $biayaPendaftaran = 50000; // Biaya dasar pendaftaran
        $biayaPoli = $this->poli->biaya ?? 0;
        $biayaDokter = $this->dokter->biaya_konsultasi ?? 0;

        return $biayaPendaftaran + $biayaPoli + $biayaDokter;
    }
}
