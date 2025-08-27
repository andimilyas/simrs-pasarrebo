<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    
    protected $fillable = [
        'user_id',
        'activity_type', // pendaftaran, pemeriksaan, resep, pembayaran, dll
        'description',
        'reference_type', // nama model yang direferensikan
        'reference_id', // ID dari model yang direferensikan
        'ip_address',
        'user_agent',
        'metadata', // JSON data tambahan
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeByReference($query, $type, $id)
    {
        return $query->where('reference_type', $type)
                    ->where('reference_id', $id);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public static function log($userId, $type, $description, $referenceType = null, $referenceId = null, $metadata = [])
    {
        return static::create([
            'user_id' => $userId,
            'activity_type' => $type,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    public function getActivityIconAttribute()
    {
        $icons = [
            'pendaftaran' => 'fas fa-user-plus',
            'pemeriksaan' => 'fas fa-stethoscope',
            'resep' => 'fas fa-prescription',
            'pembayaran' => 'fas fa-credit-card',
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'create' => 'fas fa-plus',
            'update' => 'fas fa-edit',
            'delete' => 'fas fa-trash',
        ];

        return $icons[$this->activity_type] ?? 'fas fa-info-circle';
    }

    public function getActivityColorAttribute()
    {
        $colors = [
            'pendaftaran' => 'text-info',
            'pemeriksaan' => 'text-primary',
            'resep' => 'text-success',
            'pembayaran' => 'text-warning',
            'login' => 'text-success',
            'logout' => 'text-secondary',
            'create' => 'text-success',
            'update' => 'text-warning',
            'delete' => 'text-danger',
        ];

        return $colors[$this->activity_type] ?? 'text-secondary';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
