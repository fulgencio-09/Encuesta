<?php

namespace App\Http\Controllers;

use App\Models\ReportingQuarter;
use App\Models\Survey;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        return view('admin.reports.index', $data);
    }

    public function matrix(Request $request)
    {
        $data = $this->buildReportData($request);

        return view('admin.reports.matrix', $data);
    }

    public function pdf(Request $request)
    {
        $data = $this->buildReportData($request);

        $pdf = Pdf::loadView('admin.reports.pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('isRemoteEnabled', true);

        $period = $data['periodLabelForFile'];
        return $pdf->download('informe-estadistico-encuesta-'.$period.'.pdf');
    }

    private function buildReportData(Request $request): array
    {
        $survey = Survey::with(['questions.options', 'questions.items'])->where('slug', 'satisfaccion-usuario')->firstOrFail();
        $quarters = ReportingQuarter::orderByDesc('year')->orderByDesc('quarter')->get();
        $activeQuarter = ReportingQuarter::where('is_active', true)->first();

        $year = (int) ($request->input('year') ?: ($activeQuarter?->year ?: now()->year));
        $quarterId = $request->filled('quarter_id') ? (int) $request->quarter_id : null;
        $selectedQuarter = $quarterId ? $quarters->firstWhere('id', $quarterId) : null;

        $responseQuery = $survey->responses()->with(['answers.option', 'answers.item', 'answers.question']);
        $responseQuery->whereYear('response_date', $year);

        if ($selectedQuarter) {
            $responseQuery->whereBetween('response_date', [$selectedQuarter->start_date, $selectedQuarter->end_date]);
        }

        $responses = $responseQuery->get();
        $questions = $survey->questions;

        $questionStats = $questions->map(function ($question) use ($responses) {
            $answers = $responses->flatMap(fn ($response) => $response->answers->where('question_id', $question->id));
            $total = $answers->count();

            if ($question->type === 'matrix') {
                $items = $question->items->map(function ($item) use ($answers, $question) {
                    $itemAnswers = $answers->where('question_item_id', $item->id);
                    $numeric = $itemAnswers->filter(fn ($answer) => $answer->answer_numeric !== null);
                    $options = $question->options->map(fn ($option) => [
                        'label' => $option->label,
                        'count' => $itemAnswers->where('question_option_id', $option->id)->count(),
                    ]);
                    return [
                        'label' => $item->label,
                        'total' => $itemAnswers->count(),
                        'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null,
                        'options' => $options,
                    ];
                });

                $numeric = $answers->filter(fn ($answer) => $answer->answer_numeric !== null);
                return [
                    'question' => $question,
                    'total' => $total,
                    'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null,
                    'items' => $items,
                ];
            }

            $numeric = $answers->filter(fn ($answer) => $answer->answer_numeric !== null);
            return [
                'question' => $question,
                'total' => $total,
                'average' => $numeric->count() ? round($numeric->avg('answer_numeric'), 2) : null,
                'options' => $question->options->map(fn ($option) => [
                    'label' => $option->label,
                    'count' => $answers->where('question_option_id', $option->id)->count(),
                ]),
            ];
        });

        $monthNames = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        $monthly = collect();
        foreach ($monthNames as $monthNumber => $monthName) {
            $monthResponses = $responses->filter(fn ($response) => $response->response_date && (int) $response->response_date->format('n') === $monthNumber);
            $row = ['month' => $monthName, 'total' => $monthResponses->count(), 'questions' => []];

            foreach ($questions->where('type', '!=', 'matrix') as $question) {
                $answers = $monthResponses->flatMap(fn ($response) => $response->answers->where('question_id', $question->id));
                $row['questions'][$question->question_key] = $question->options->mapWithKeys(fn ($option) => [
                    $option->value => $answers->where('question_option_id', $option->id)->count(),
                ])->all();
            }

            $monthly->push($row);
        }

        $matrixStats = $questionStats->firstWhere('question.question_key', 'atencion_servicios');
        $experience = $questionStats->firstWhere('question.question_key', 'experiencia_global');
        $cleaning = $questionStats->firstWhere('question.question_key', 'limpieza_desinfeccion');

        $favorable = 0;
        if ($experience && $experience['total']) {
            $favorable = $experience['options']->whereIn('label', ['Muy Buena', 'Buena'])->sum('count');
            $favorable = round(($favorable / $experience['total']) * 100, 1);
        }

        $suggestionQuestion = $questions->firstWhere('question_key', 'sugerencias');
        $suggestions = $suggestionQuestion ? $responses->flatMap(function ($response) use ($suggestionQuestion) {
            return $response->answers->where('question_id', $suggestionQuestion->id)
                ->filter(fn ($answer) => filled($answer->answer_text))
                ->map(fn ($answer) => ['date' => optional($response->response_date)->format('Y-m-d'), 'text' => $answer->answer_text]);
        })->values() : collect();

        $years = ReportingQuarter::select('year')->distinct()->orderByDesc('year')->pluck('year');
        if (!$years->contains($year)) {
            $years->push($year);
            $years = $years->sortDesc()->values();
        }

        $periodLabel = $selectedQuarter
            ? $selectedQuarter->name.' '.$selectedQuarter->year
            : 'Año '.$year;
        $periodLabelForFile = $selectedQuarter
            ? $selectedQuarter->year.'-T'.$selectedQuarter->quarter
            : (string) $year;

        return compact(
            'survey', 'quarters', 'selectedQuarter', 'activeQuarter', 'year', 'years',
            'responses', 'questions', 'questionStats', 'monthly', 'monthNames',
            'matrixStats', 'experience', 'cleaning', 'favorable', 'suggestions',
            'periodLabel', 'periodLabelForFile'
        );
    }
}
