@extends('layouts.app')

@section('content')
<style>
.dashboard{max-width:1400px;margin:auto}.hero{background:linear-gradient(135deg,#0f3d56,#176b87);color:#fff;border-radius:16px;padding:24px;margin-bottom:18px}.hero h1{margin:0 0 6px}.filters,.card{background:#fff;border-radius:14px;padding:20px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:18px}.filters-grid{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:end}.field label{display:block;font-size:12px;font-weight:700;margin-bottom:5px}.field select{width:100%;height:44px;box-sizing:border-box;padding:10px;border:1px solid #d7dde3;border-radius:8px;background:#fff}.btn{display:inline-flex;align-items:center;justify-content:center;box-sizing:border-box;height:44px;border:0;border-radius:8px;padding:10px 16px;text-decoration:none;cursor:pointer;font-weight:700;white-space:nowrap}.primary{background:#176b87;color:#fff}.light{background:#eef2f5;color:#263238}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}.stat strong{display:block;font-size:30px;margin-top:5px}.stat span,.muted{color:#667085;font-size:13px}.bars .row{margin:12px 0}.bar{height:9px;background:#e7ebef;border-radius:10px;overflow:hidden}.fill{height:100%;background:#176b87}.matrix{width:100%;border-collapse:collapse}.matrix th,.matrix td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left}.menu{display:flex;gap:10px;flex-wrap:wrap;margin-top:15px}@media(max-width:700px){.filters-grid,.stats{grid-template-columns:1fr}.filters-grid .btn{width:100%}.matrix{font-size:13px;display:block;overflow-x:auto}}
</style>

<div class="dashboard">
    <div class="hero">
        <h1>Dashboard de Satisfacción</h1>
        @if($selectedQuarter)
            <div style="font-size:20px;font-weight:700;margin-top:4px">ENCUESTA DE SATISFACCIÓN DEL USUARIO {{ $selectedQuarter->year }}</div>
            <div style="margin-top:8px;opacity:.9">Periodo seleccionado: <strong>{{ $selectedQuarter->name }}</strong></div>
        @else
            <div style="font-size:20px;font-weight:700;margin-top:4px">ENCUESTA DE SATISFACCIÓN DEL USUARIO</div>
            <div style="margin-top:8px;opacity:.9">No hay un trimestre activo seleccionado.</div>
        @endif
        <div class="menu">
            <a class="btn light" href="{{ route('admin.quarters.index') }}">⚙ Parametrizar trimestres</a>
            <a class="btn light" href="{{ route('admin.surveys.results', $survey->slug) }}">📊 Ver resultados detallados</a>
            <a class="btn light" href="{{ route('admin.reports.index') }}">📄 Informes y PDF</a>
        </div>
    </div>

    <form method="GET" class="filters">
        <h3 style="margin-top:0">Periodo de consulta</h3>
        <div class="filters-grid">
            <div class="field"><label>Trimestre</label><select name="quarter_id"><option value="">Todos / seleccionar año</option>@foreach($quarters as $quarter)<option value="{{ $quarter->id }}" @selected($selectedQuarter?->id === $quarter->id)>{{ $quarter->name }}</option>@endforeach</select></div>
            <div class="field"><label>Año</label><select name="year"><option value="">Todos</option>@foreach($years as $year)<option value="{{ $year }}" @selected(request('year') == $year)>{{ $year }}</option>@endforeach</select></div>
            <button class="btn primary" type="submit">Aplicar periodo</button>
        </div>
    </form>

    <div class="stats">
        <div class="card"><span>Encuestas del periodo</span><strong>{{ $responses->count() }}</strong></div>
        <div class="card"><span>Preguntas</span><strong>{{ $survey->questions->count() }}</strong></div>
        <div class="card"><span>Periodo</span><strong style="font-size:20px">{{ $selectedQuarter?->name ?? 'Todos' }}</strong></div>
    </div>

    @foreach($questionStats as $stat)
        <div class="card">
            <h2 style="margin-top:0">{{ $stat['question']->position }}. {{ $stat['question']->question_text }}</h2>
            @if($stat['question']->type === 'matrix')
                <table class="matrix"><thead><tr><th>Servicio</th><th>Promedio</th></tr></thead><tbody>@foreach($stat['items'] as $item)<tr><td>{{ $item['label'] }}</td><td><strong>{{ $item['average'] !== null ? $item['average'].' / 5' : '—' }}</strong></td></tr>@endforeach</tbody></table>
            @elseif($stat['question']->type === 'textarea')
                <p class="muted">Las observaciones se encuentran en el módulo de resultados detallados.</p>
            @else
                <div class="bars">@foreach($stat['options'] as $option)<div class="row"><div style="display:flex;justify-content:space-between"><span>{{ $option['label'] }}</span><strong>{{ $option['count'] }} ({{ $option['percent'] }}%)</strong></div><div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div></div>@endforeach</div>
            @endif
        </div>
    @endforeach
</div>
@endsection
