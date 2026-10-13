<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'allow_multiple_responses',
        'published_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'allow_multiple_responses' => 'boolean',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
