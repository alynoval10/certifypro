<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScoreComponent extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_score', 'weight', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['max_score' => 'decimal:2', 'weight' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function internshipScores(): HasMany
    {
        return $this->hasMany(InternshipScore::class);
    }
}
