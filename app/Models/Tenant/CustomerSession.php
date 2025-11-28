<?php

namespace App\Models\Tenant;

class CustomerSession extends TenantModel
{
    protected $fillable = [
        'customer_id',
        'nas_id',
        'username',
        'session_id',
        'nas_ip_address',
        'nas_port_id',
        'framed_ip_address',
        'calling_station_id',
        'called_station_id',
        'session_start',
        'session_stop',
        'session_time',
        'input_octets',
        'output_octets',
        'terminate_cause',
        'service_type',
    ];

    protected $casts = [
        'session_start' => 'datetime',
        'session_stop' => 'datetime',
        'session_time' => 'integer',
        'input_octets' => 'integer',
        'output_octets' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function nas()
    {
        return $this->belongsTo(Nas::class);
    }

    public function isActive(): bool
    {
        return is_null($this->session_stop);
    }

    public function getSessionDurationAttribute(): string
    {
        if (!$this->session_time) {
            return '-';
        }
        
        $hours = floor($this->session_time / 3600);
        $minutes = floor(($this->session_time % 3600) / 60);
        $seconds = $this->session_time % 60;
        
        if ($hours > 0) {
            return sprintf('%d jam %d menit', $hours, $minutes);
        }
        if ($minutes > 0) {
            return sprintf('%d menit %d detik', $minutes, $seconds);
        }
        return sprintf('%d detik', $seconds);
    }

    public function getTotalTrafficAttribute(): int
    {
        return $this->input_octets + $this->output_octets;
    }

    public function getTrafficTextAttribute(): string
    {
        $bytes = $this->total_traffic;
        
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getDownloadTextAttribute(): string
    {
        $bytes = $this->input_octets;
        
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getUploadTextAttribute(): string
    {
        $bytes = $this->output_octets;
        
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    public function scopeActive($query)
    {
        return $query->whereNull('session_stop');
    }

    public function scopeHotspot($query)
    {
        return $query->where('service_type', 'hotspot');
    }

    public function scopePppoe($query)
    {
        return $query->where('service_type', 'pppoe');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('session_start', today());
    }
}
