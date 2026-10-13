<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SurveyController extends Controller
{
    public function show(string $slug)
    {
        $survey = Survey::with([
            'questions' => fn ($query) => $query->orderBy('position'),
            'questions.options' => fn ($query) => $query->where('is_active', true)->orderBy('position'),
            'questions.items' => fn ($query) => $query->where('is_active', true)->orderBy('position'),
        ])->where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($survey->closed_at && now()->greaterThan($survey->closed_at)) {
            abort(404);
        }

        return view('surveys.show', compact('survey'));
    }

    public function store(Request $request, string $slug)
    {
        $survey = Survey::with(['questions.options', 'questions.items'])
            ->where('slug', $slug)->where('is_active', true)->firstOrFail();

        if ($survey->closed_at && now()->greaterThan($survey->closed_at)) {
            abort(404);
        }

        $rules = [
            'respondent_name' => ['nullable', 'string', 'max:150'],
            'document_type' => ['nullable', 'string', 'max:50'],
            'document_number' => ['nullable', 'string', 'max:50'],
            'care_center' => ['nullable', 'string', 'max:150'],
            'response_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'max:30'],
            'pregnancy' => ['nullable', 'string', 'max:30'],
            'age' => ['nullable', 'integer', 'min:0', 'max:120'],
            'eps' => ['nullable', 'string', 'max:150'],
            'regime' => ['nullable', 'string', 'max:80'],
            'answers' => ['array'],
        ];

        foreach ($survey->questions as $question) {
            $key = 'answers.' . $question->question_key;
            $allowed = $question->options->pluck('value')->all();

            if ($question->type === 'matrix') {
                $rules[$key] = [$question->is_required ? 'required' : 'nullable', 'array'];
                foreach ($question->items as $item) {
                    $rules[$key . '.' . $item->value] = [
                        $question->is_required ? 'required' : 'nullable',
                        'string',
                        'in:' . implode(',', $allowed),
                    ];
                }
            } elseif ($question->type === 'textarea') {
                $rules[$key] = [$question->is_required ? 'required' : 'nullable', 'string', 'max:5000'];
            } else {
                $rules[$key] = [$question->is_required ? 'required' : 'nullable', 'string', 'in:' . implode(',', $allowed)];
            }
        }

        $validated = $request->validate($rules, [
            'required' => 'Este campo es obligatorio.',
            'in' => 'Seleccione una opción válida.',
            'array' => 'Complete las opciones solicitadas.',
        ]);

        DB::transaction(function () use ($request, $validated, $survey) {
            $response = SurveyResponse::create([
                'survey_id' => $survey->id,
                'respondent_name' => $validated['respondent_name'] ?? null,
                'document_type' => $validated['document_type'] ?? null,
                'document_number' => $validated['document_number'] ?? null,
                'care_center' => $validated['care_center'] ?? null,
                'response_date' => $validated['response_date'] ?? now()->toDateString(),
                'gender' => $validated['gender'] ?? null,
                'pregnancy' => $validated['pregnancy'] ?? null,
                'age' => $validated['age'] ?? null,
                'eps' => $validated['eps'] ?? null,
                'regime' => $validated['regime'] ?? null,
                'submitted_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), 1000, ''),
            ]);

            foreach ($survey->questions as $question) {
                $value = $validated['answers'][$question->question_key] ?? null;

                if ($question->type === 'matrix') {
                    foreach (($value ?? []) as $itemValue => $optionValue) {
                        $item = $question->items->firstWhere('value', $itemValue);
                        $option = $question->options->firstWhere('value', $optionValue);
                        if (!$item || !$option) continue;

                        $response->answers()->create([
                            'question_id' => $question->id,
                            'question_item_id' => $item->id,
                            'question_option_id' => $option->id,
                            'answer_numeric' => is_numeric($option->value) ? (float) $option->value : null,
                        ]);
                    }
                    continue;
                }

                if ($question->type === 'textarea') {
                    if ($value !== null && $value !== '') {
                        $response->answers()->create(['question_id' => $question->id, 'answer_text' => $value]);
                    }
                    continue;
                }

                if ($value !== null && $value !== '') {
                    $option = $question->options->firstWhere('value', $value);
                    $response->answers()->create([
                        'question_id' => $question->id,
                        'question_option_id' => $option?->id,
                        'answer_text' => $option ? null : $value,
                        'answer_numeric' => $option && is_numeric($option->value) ? (float) $option->value : null,
                    ]);
                }
            }
        });

        return view('surveys.thanks', compact('survey'));
    }
}
