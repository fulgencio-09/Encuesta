<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class AdminSurveyController extends Controller
{
    public function results(Request $request, string $slug)
    {
        $survey = Survey::with(['questions.options', 'questions.items'])
            ->where('slug', $slug)
            ->firstOrFail();

        $responseQuery = $survey->responses()
            ->with(['answers.option', 'answers.item', 'answers.question'])
            ->latest('submitted_at');

        if ($request->filled('date_from')) {
            $responseQuery->whereDate('response_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $responseQuery->whereDate('response_date', '<=', $request->date_to);
        }

        foreach (['care_center', 'eps', 'regime', 'gender'] as $field) {
            if ($request->filled($field)) {
                $responseQuery->where($field, $request->input($field));
            }
        }

        if ($request->filled('age_from')) {
            $responseQuery->where('age', '>=', (int) $request->age_from);
        }

        if ($request->filled('age_to')) {
            $responseQuery->where('age', '<=', (int) $request->age_to);
        }

        $responses = $responseQuery->get();

        $questionStats = $survey->questions->map(function ($question) use ($responses) {
            $answers = $responses->flatMap(function ($response) use ($question) {
                return $response->answers->where('question_id', $question->id);
            });

            if ($question->type === 'matrix') {
                $items = $question->items->map(function ($item) use ($answers, $question) {
                    $itemAnswers = $answers->where('question_item_id', $item->id);
                    $total = $itemAnswers->count();
                    $options = $question->options->map(function ($option) use ($itemAnswers, $total) {
                        $count = $itemAnswers->where('question_option_id', $option->id)->count();
                        return [
                            'label' => $option->label,
                            'count' => $count,
                            'percent' => $total ? round(($count / $total) * 100, 1) : 0,
                        ];
                    });
                    $numeric = $itemAnswers->filter(fn ($answer) => $answer->answer_numeric !== null);
                    return [
                        'label' => $item->label,
                        'total' => $total,
                        'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null,
                        'options' => $options,
                    ];
                });

                $matrixNumeric = $answers->filter(fn ($answer) => $answer->answer_numeric !== null);

                return [
                    'question' => $question,
                    'total' => $answers->count(),
                    'average' => $matrixNumeric->count() ? round($matrixNumeric->avg('answer_numeric'), 2) : null,
                    'items' => $items,
                ];
            }

            $total = $answers->count();
            $options = $question->options->map(function ($option) use ($answers, $total) {
                $count = $answers->where('question_option_id', $option->id)->count();
                return [
                    'label' => $option->label,
                    'count' => $count,
                    'percent' => $total ? round(($count / $total) * 100, 1) : 0,
                ];
            });

            $numeric = $answers->filter(fn ($answer) => $answer->answer_numeric !== null);

            return [
                'question' => $question,
                'total' => $total,
                'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null,
                'options' => $options,
            ];
        });

        $filterValues = [
            'care_centers' => $survey->responses()->whereNotNull('care_center')->where('care_center', '!=', '')->distinct()->orderBy('care_center')->pluck('care_center'),
            'eps_list' => $survey->responses()->whereNotNull('eps')->where('eps', '!=', '')->distinct()->orderBy('eps')->pluck('eps'),
            'regimes' => $survey->responses()->whereNotNull('regime')->where('regime', '!=', '')->distinct()->orderBy('regime')->pluck('regime'),
            'genders' => $survey->responses()->whereNotNull('gender')->where('gender', '!=', '')->distinct()->orderBy('gender')->pluck('gender'),
        ];

        $suggestionQuestionId = $survey->questions->firstWhere('question_key', 'sugerencias')?->id;
        $suggestions = $suggestionQuestionId
            ? $responses->flatMap(function ($response) use ($suggestionQuestionId) {
                return $response->answers
                    ->where('question_id', $suggestionQuestionId)
                    ->filter(fn ($answer) => filled($answer->answer_text))
                    ->map(fn ($answer) => [
                        'date' => optional($response->response_date)->format('Y-m-d'),
                        'text' => $answer->answer_text,
                    ]);
            })->values()
            : collect();

        $experience = $questionStats->firstWhere('question.question_key', 'experiencia_global');
        $cleaning = $questionStats->firstWhere('question.question_key', 'limpieza_desinfeccion');
        $matrix = $questionStats->firstWhere('question.question_key', 'atencion_servicios');

        $favorableExperience = 0;
        if ($experience) {
            $favorableExperience = $experience['options']
                ->whereIn('label', ['Muy Buena', 'Buena'])
                ->sum('count');
            $favorableExperience = $experience['total']
                ? round(($favorableExperience / $experience['total']) * 100, 1)
                : 0;
        }

        return view('admin.surveys.results', [
            'survey' => $survey,
            'responses' => $responses,
            'questionStats' => $questionStats,
            'filterValues' => $filterValues,
            'suggestions' => $suggestions,
            'summary' => [
                'total' => $responses->count(),
                'favorable_experience' => $favorableExperience,
                'cleaning_average' => $cleaning['average'] ?? null,
                'services_average' => $matrix['average'] ?? null,
            ],
            'filters' => $request->only(['date_from', 'date_to', 'care_center', 'eps', 'regime', 'gender', 'age_from', 'age_to']),
        ]);
    }
}
