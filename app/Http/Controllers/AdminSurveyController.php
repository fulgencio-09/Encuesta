<?php

namespace App\Http\Controllers;

use App\Models\Survey;

class AdminSurveyController extends Controller
{
    public function results(string $slug)
    {
        $survey = Survey::with([
            'questions.options',
            'questions.items',
            'responses.answers.option',
            'responses.answers.item',
        ])->where('slug', $slug)->firstOrFail();

        $responses = $survey->responses;

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
                        return ['label' => $option->label, 'count' => $count, 'percent' => $total ? round(($count / $total) * 100, 1) : 0];
                    });
                    $numeric = $itemAnswers->filter(fn ($answer) => $answer->answer_numeric !== null);
                    return ['label' => $item->label, 'total' => $total, 'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null, 'options' => $options];
                });
                return ['question' => $question, 'total' => $answers->count(), 'items' => $items];
            }

            $total = $answers->count();
            $options = $question->options->map(function ($option) use ($answers, $total) {
                $count = $answers->where('question_option_id', $option->id)->count();
                return ['label' => $option->label, 'count' => $count, 'percent' => $total ? round(($count / $total) * 100, 1) : 0];
            });

            return ['question' => $question, 'total' => $total, 'options' => $options];
        });

        return view('admin.surveys.results', compact('survey', 'responses', 'questionStats'));
    }
}
