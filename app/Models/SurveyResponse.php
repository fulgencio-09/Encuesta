<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'respondent_name',
        'document_type',
        'document_number',
        'care_center',
        'response_date',
        'gender',
        'pregnancy',
        'age',
        'eps',
        'regime',
        'submitted_at',
        'ip_address',
        'user_agent',
    ];

    protected $hidden = [
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'response_date' => 'date',
            'submitted_at' => 'datetime',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyResponseAnswer::class);
    }
}
