<?php

namespace App\Models\Tenant;

class Radacct extends TenantModel
{
    protected $table = 'radacct';
    protected $primaryKey = 'radacctid';
    
    protected $fillable = [
        'acctsessionid',
        'acctuniqueid',
        'username',
        'realm',
        'nasipaddress',
        'nasportid',
        'nasporttype',
        'acctstarttime',
        'acctupdatetime',
        'acctstoptime',
        'acctinterval',
        'acctsessiontime',
        'acctauthentic',
        'connectinfo_start',
        'connectinfo_stop',
        'acctinputoctets',
        'acctoutputoctets',
        'calledstationid',
        'callingstationid',
        'acctterminatecause',
        'servicetype',
        'framedprotocol',
        'framedipaddress',
        'framedipv6address',
        'framedipv6prefix',
        'framedinterfaceid',
        'delegatedipv6prefix',
        'class',
    ];

    protected $casts = [
        'acctstarttime' => 'datetime',
        'acctupdatetime' => 'datetime',
        'acctstoptime' => 'datetime',
        'acctinputoctets' => 'integer',
        'acctoutputoctets' => 'integer',
        'acctsessiontime' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->whereNull('acctstoptime');
    }

    public function scopeByUsername($query, string $username)
    {
        return $query->where('username', $username);
    }

    public function scopeByNas($query, string $nasIpAddress)
    {
        return $query->where('nasipaddress', $nasIpAddress);
    }

    public function getDownloadBytesAttribute(): int
    {
        return $this->acctoutputoctets ?? 0;
    }

    public function getUploadBytesAttribute(): int
    {
        return $this->acctinputoctets ?? 0;
    }

    public function getTotalBytesAttribute(): int
    {
        return $this->download_bytes + $this->upload_bytes;
    }

    public function getFormattedDownloadAttribute(): string
    {
        return $this->formatBytes($this->download_bytes);
    }

    public function getFormattedUploadAttribute(): string
    {
        return $this->formatBytes($this->upload_bytes);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->formatBytes($this->total_bytes);
    }

    public function getSessionDurationAttribute(): string
    {
        $seconds = $this->acctsessiontime ?? 0;
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
