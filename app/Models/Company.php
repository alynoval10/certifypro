<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'industry',
        'contact_name',
        'email',
        'phone',
        'address',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }
}
