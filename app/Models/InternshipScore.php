<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipScore extends Model
{
    use HasFactory;

    protected $fillable = ['internship_id', 'score_component_id', 'score', 'note'];

    protected function casts(): array
    {
        return ['score' => 'decimal:2'];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(ScoreComponent::class, 'score_component_id');
    }
}
