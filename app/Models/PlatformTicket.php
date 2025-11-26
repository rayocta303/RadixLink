<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformTicket extends Model
{
    protected $fillable = [
        'tenant_id',
        'user_id',
        'ticket_number',
        'subject',
        'message',
        'priority',
        'status',
        'category',
        'assigned_to',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(PlatformTicketReply::class, 'ticket_id');
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting']);
    }
}
