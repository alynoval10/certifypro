<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'internship_id',
        'certificate_template_id',
        'certificate_number',
        'verification_code',
        'issued_at',
        'predicate',
        'description',
        'status',
        'revoked_at',
        'revoke_reason',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'revoked_at' => 'datetime',
        ];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }
}
