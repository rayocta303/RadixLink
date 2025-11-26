<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class VoucherTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'html_template',
        'css_styles',
        'paper_size',
        'orientation',
        'vouchers_per_page',
        'show_qr_code',
        'show_logo',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'show_qr_code' => 'boolean',
        'show_logo' => 'boolean',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
