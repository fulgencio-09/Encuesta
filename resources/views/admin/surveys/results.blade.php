@extends('layouts.app')

@section('content')
<style>
    .stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin:20px 0}.stat{background:#fff;border-radius:12px;padding:22px;box-shadow:0 4px 18px rgba(0,0,0,.06)}.stat strong{display:block;font-size:30px}.question{background:#fff;border-radius:12px;padding:24px;margin-bottom:20px;box-shadow:0 4px 18px rgba(0,0,0,.06)}.option{margin:12px 0}.bar{height:9px;background:#e5e7eb;border-radius:10px;overflow:hidden}.fill{height:100%;background:#2563eb}.muted{color:#6b7280}.matrix{width:100%;border-collapse:collapse;margin-top:15px}.matrix th,.matrix td{padding:9px;border-bottom:1px solid #e5e7eb;text-align:left}.badge{display:inline-block;background:#eef2ff;color:#3730a3;padding:6px 10px;border-radius:20px;font-size:13px}
    @media(max-width:800px){.stats-grid{grid-template-columns:1fr}.matrix{font-size:13px;display:block;overflow-x:auto}}
</style>

<div class="card">
    <h1>Resultados de encuesta</h1>
    <p class="muted">{{ $survey->name }}</p>
    <span class="badge">Resultados administrativos</span>
</div>

<div class="stats-grid">
    <div class="stat"><span class="muted">Total de respuestas</span><strong>{{ $responses->count() }}</strong></div>
    <div class="stat"><span class="muted">Preguntas</span><strong>{{ $survey->questions->count() }}</strong></div>
    <div class="stat"><span class="muted">Estado</span><strong style="font-size:22px">{{ $survey->is_active ? 'Activa' : 'Inactiva' }}</strong></div>
</div>

@foreach($questionStats as $stat)
    <section class="question">
        <h2>{{ $stat['question']->position }}. {{ $stat['question']->question_text }}</h2>

        @if($stat['question']->type === 'matrix')
            <table class="matrix">
                <thead><tr><th>Servicio</th><th>Promedio</th><th>Respuestas</th><th>Distribución</th></tr></thead>
                <tbody>
                @foreach($stat['items'] as $item)
                    <tr>
                        <td><strong>{{ $item['label'] }}</strong></td>
                        <td>{{ $item['average'] !== null ? $item['average'] : '—' }}</td>
                        <td>{{ $item['total'] }}</td>
                        <td>
                            @foreach($item['options'] as $option)
                                <div style="margin-bottom:7px"><small>{{ $option['label'] }} — {{ $option['count'] }} ({{ $option['percent'] }}%)</small><div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div></div>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @elseif($stat['question']->type === 'textarea')
            <p class="muted">Pregunta abierta. Las respuestas individuales se conservarán para el módulo de observaciones/recomendaciones.</p>
        @else
            @foreach($stat['options'] as $option)
                <div class="option">
                    <div style="display:flex;justify-content:space-between;gap:15px"><span>{{ $option['label'] }}</span><strong>{{ $option['count'] }} ({{ $option['percent'] }}%)</strong></div>
                    <div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div>
                </div>
            @endforeach
            <p class="muted">Total de respuestas registradas: {{ $stat['total'] }}</p>
        @endif
    </section>
@endforeach
@endsection
