<?php

namespace App\Http\Controllers;

use App\Models\ReportingQuarter;
use App\Models\Survey;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $survey = Survey::with(['questions.options', 'questions.items'])->where('slug', 'satisfaccion-usuario')->firstOrFail();
        $quarters = ReportingQuarter::where('is_active', true)->orderByDesc('year')->orderByDesc('quarter')->get();
        $selectedQuarter = $request->filled('quarter_id')
            ? $quarters->firstWhere('id', (int) $request->quarter_id)
            : $quarters->first();

        $responseQuery = $survey->responses()->with(['answers.option', 'answers.item', 'answers.question']);
        if ($selectedQuarter) {
            $responseQuery->whereBetween('response_date', [$selectedQuarter->start_date, $selectedQuarter->end_date]);
        } elseif ($request->filled('year')) {
            $responseQuery->whereYear('response_date', $request->year);
        }

        $responses = $responseQuery->get();
        $questionStats = $survey->questions->map(function ($question) use ($responses) {
            $answers = $responses->flatMap(fn ($response) => $response->answers->where('question_id', $question->id));
            if ($question->type === 'matrix') {
                $items = $question->items->map(function ($item) use ($answers) {
                    $itemAnswers = $answers->where('question_item_id', $item->id);
                    $numeric = $itemAnswers->filter(fn ($a) => $a->answer_numeric !== null);
                    return ['label' => $item->label, 'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null];
                });
                return ['question' => $question, 'items' => $items];
            }
            $total = $answers->count();
            $options = $question->options->map(function ($option) use ($answers, $total) {
                $count = $answers->where('question_option_id', $option->id)->count();
                return ['label' => $option->label, 'count' => $count, 'percent' => $total ? round($count / $total * 100, 1) : 0];
            });
            return ['question' => $question, 'options' => $options, 'total' => $total];
        });

        $years = ReportingQuarter::select('year')->distinct()->orderByDesc('year')->pluck('year');
        return view('home', compact('survey', 'quarters', 'selectedQuarter', 'responses', 'questionStats', 'years'));
    }
}
