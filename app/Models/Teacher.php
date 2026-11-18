<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    protected $fillable = ['name', 'nip', 'email', 'phone'];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class, 'school_supervisor_id');
    }
}
