<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number',
        'name',
        'status',
        'class_id',
        'major_id',
        'class_name',
        'major',
        'gender',
        'email',
        'phone',
        'address',
    ];

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function majorRelation(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
}
