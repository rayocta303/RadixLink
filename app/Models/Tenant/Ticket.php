<?php

namespace App\Models\Tenant;

class Ticket extends TenantModel
{
    protected $fillable = [
        'customer_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(TenantUser::class, 'assigned_to');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting']);
    }
}
