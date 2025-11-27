<?php

namespace App\Models\Tenant;

class TicketReply extends TenantModel
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'customer_id',
        'message',
        'attachments',
        'is_internal',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal' => 'boolean',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
