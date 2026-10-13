<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_id',
        'question_key',
        'question_text',
        'type',
        'position',
        'is_required',
        'help_text',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('position');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuestionItem::class)->orderBy('position');
    }
}
