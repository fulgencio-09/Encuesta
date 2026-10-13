@extends('layouts.app')

@section('content')
<style>
    .admin-wrap{max-width:1400px;margin:0 auto;padding:10px}.hero{background:linear-gradient(135deg,#0f3d56,#176b87);color:#fff;border-radius:16px;padding:28px;margin-bottom:20px}.hero h1{margin:0 0 6px;font-size:30px}.hero p{margin:0;opacity:.9}.filters,.stat,.question,.suggestions{background:#fff;border-radius:14px;padding:20px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:20px}.filters-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px}.filters label{font-size:12px;font-weight:700;display:block;margin-bottom:5px}.filters input,.filters select{width:100%;box-sizing:border-box;padding:10px;border:1px solid #d7dde3;border-radius:8px;background:#fff}.actions{display:flex;gap:10px;align-items:end}.btn{display:inline-block;padding:10px 16px;border-radius:8px;text-decoration:none;border:0;cursor:pointer;font-weight:600}.btn-primary{background:#176b87;color:#fff}.btn-light{background:#eef2f5;color:#263238}.stats-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.stat span{color:#667085;font-size:13px}.stat strong{display:block;font-size:30px;margin-top:5px}.question h2{font-size:20px;margin-top:0}.option{margin:14px 0}.bar{height:9px;background:#e7ebef;border-radius:10px;overflow:hidden}.fill{height:100%;background:#176b87;border-radius:10px}.matrix{width:100%;border-collapse:collapse;margin-top:15px}.matrix th,.matrix td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}.badge{display:inline-block;background:#e8f4f7;color:#14556c;padding:6px 10px;border-radius:20px;font-size:13px;margin-top:14px}.muted{color:#667085}.suggestion{border-left:4px solid #176b87;background:#f8fafb;padding:12px 15px;margin:10px 0;border-radius:0 8px 8px 0}.empty{padding:20px;text-align:center;color:#667085;background:#f8fafb;border-radius:10px}@media(max-width:1100px){.filters-grid{grid-template-columns:repeat(3,1fr)}}@media(max-width:700px){.filters-grid,.stats-grid{grid-template-columns:1fr}.matrix{font-size:13px;display:block;overflow-x:auto}.hero h1{font-size:24px}}
</style>

<div class="admin-wrap">
    <div class="hero">
        <h1>Resultados de encuesta</h1>
        <p>{{ $survey->name }}</p>
        <span class="badge">Panel administrativo</span>
    </div>

    <form method="GET" class="filters">
        <h3 style="margin-top:0">Filtros de resultados</h3>
        <div class="filters-grid">
            <div><label>Desde</label><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
            <div><label>Hasta</label><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
            <div><label>Centro de atención</label><select name="care_center"><option value="">Todos</option>@foreach($filterValues['care_centers'] as $value)<option value="{{ $value }}" @selected(($filters['care_center'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>EPS</label><select name="eps"><option value="">Todas</option>@foreach($filterValues['eps_list'] as $value)<option value="{{ $value }}" @selected(($filters['eps'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>Régimen</label><select name="regime"><option value="">Todos</option>@foreach($filterValues['regimes'] as $value)<option value="{{ $value }}" @selected(($filters['regime'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>Género</label><select name="gender"><option value="">Todos</option>@foreach($filterValues['genders'] as $value)<option value="{{ $value }}" @selected(($filters['gender'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
        </div>
        <div class="actions" style="margin-top:14px">
            <button class="btn btn-primary" type="submit">Aplicar filtros</button>
            <a class="btn btn-light" href="{{ route('admin.surveys.results', $survey->slug) }}">Limpiar</a>
        </div>
    </form>

    <div class="stats-grid">
        <div class="stat"><span>Respuestas con filtros</span><strong>{{ $responses->count() }}</strong></div>
        <div class="stat"><span>Preguntas</span><strong>{{ $survey->questions->count() }}</strong></div>
        <div class="stat"><span>Estado</span><strong style="font-size:22px">{{ $survey->is_active ? 'Activa' : 'Inactiva' }}</strong></div>
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
                            <td>{{ $item['average'] !== null ? $item['average'].' / 5' : '—' }}</td>
                            <td>{{ $item['total'] }}</td>
                            <td>@foreach($item['options'] as $option)<div style="margin-bottom:7px"><small>{{ $option['label'] }} — {{ $option['count'] }} ({{ $option['percent'] }}%)</small><div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div></div>@endforeach</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @elseif($stat['question']->type === 'textarea')
                <p class="muted">Las sugerencias registradas se muestran en el apartado de observaciones.</p>
            @else
                @foreach($stat['options'] as $option)
                    <div class="option"><div style="display:flex;justify-content:space-between;gap:15px"><span>{{ $option['label'] }}</span><strong>{{ $option['count'] }} ({{ $option['percent'] }}%)</strong></div><div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div></div>
                @endforeach
                <p class="muted">Total de respuestas registradas para esta pregunta: {{ $stat['total'] }}</p>
            @endif
        </section>
    @endforeach

    <section class="suggestions">
        <h2>Observaciones y recomendaciones</h2>
        @forelse($suggestions as $suggestion)
            <div class="suggestion"><small class="muted">{{ $suggestion['date'] }}</small><div>{{ $suggestion['text'] }}</div></div>
        @empty
            <div class="empty">No hay sugerencias para los filtros seleccionados.</div>
        @endforelse
    </section>
</div>
@endsection
