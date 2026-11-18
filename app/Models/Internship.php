<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Internship extends Model
{
    protected $fillable = [
        'student_id',
        'company_id',
        'academic_year',
        'academic_year_id',
        'department',
        'mentor_name',
        'school_supervisor',
        'school_supervisor_id',
        'start_date',
        'end_date',
        'score',
        'final_note',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'score' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolSupervisor(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'school_supervisor_id');
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(InternshipScore::class);
    }

    public function recalculateFinalScore(): void
    {
        $scores = $this->scores()->with('component')->whereNotNull('score')->get();
        $weightTotal = $scores->sum(fn (InternshipScore $score): float => (float) $score->component->weight);

        if ($weightTotal <= 0) {
            $this->update(['score' => null]);

            return;
        }

        $weightedTotal = $scores->sum(function (InternshipScore $score): float {
            $maximum = (float) $score->component->max_score;

            return $maximum > 0
                ? ((float) $score->score / $maximum * 100) * (float) $score->component->weight
                : 0;
        });

        $this->update(['score' => round($weightedTotal / $weightTotal, 2)]);
    }
}
