<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'activity_type',
        'entity_type',
        'entity_id',
        'metadata',
        'referrer',
        'device_type',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        if (!$this->entity_type || !$this->entity_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . ucfirst($this->entity_type);
        
        if (class_exists($modelClass)) {
            return $modelClass::find($this->entity_id);
        }

        return null;
    }

    public static function logActivity($type, $entityType = null, $entityId = null, $metadata = [])
    {
        return self::create([
            'user_id' => auth()->id(),
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'activity_type' => $type,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata,
            'referrer' => request()->header('referer'),
            'device_type' => self::detectDevice(),
        ]);
    }

    private static function detectDevice()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/mobile/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    public function scopeByType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeInPeriod($query, $startDate, $endDate = null)
    {
        $endDate = $endDate ?? now();
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}