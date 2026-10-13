<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_response_id',
        'question_id',
        'question_item_id',
        'question_option_id',
        'answer_text',
        'answer_numeric',
    ];

    protected function casts(): array
    {
        return ['answer_numeric' => 'decimal:2'];
    }

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'survey_response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(QuestionItem::class, 'question_item_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
