<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name', 'front_heading', 'front_subheading', 'back_heading',
        'front_company_sign_label', 'front_school_sign_label', 'back_mentor_sign_label',
        'primary_color', 'accent_color', 'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'certificate_template_id');
    }
}
