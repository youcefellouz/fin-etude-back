<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartAlert extends Model
{
    protected $fillable = [
        'alert_type',
        'severity',
        'title',
        'message',
        'metadata',
        'related_entity_type',
        'related_entity_id',
        'recommended_action',
        'is_read',
        'is_resolved',
        'resolved_at',
        'resolution_note',
        'importance_score',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_read' => 'boolean',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'importance_score' => 'decimal:2',
    ];

    public function relatedEntity()
    {
        if (!$this->related_entity_type || !$this->related_entity_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . ucfirst($this->related_entity_type);
        
        if (class_exists($modelClass)) {
            return $modelClass::find($this->related_entity_id);
        }

        return null;
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function markAsResolved($note = null)
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);
    }
}