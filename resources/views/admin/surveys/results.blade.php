@extends('layouts.app')

@section('content')
<style>
    .admin-wrap{max-width:1450px;margin:0 auto;padding:18px}.hero{background:linear-gradient(135deg,#0f3d56,#176b87);color:#fff;border-radius:18px;padding:28px 30px;margin-bottom:20px;box-shadow:0 8px 25px rgba(15,61,86,.18)}.hero-row{display:flex;justify-content:space-between;gap:20px;align-items:center}.hero h1{margin:0 0 6px;font-size:30px}.hero p{margin:0;opacity:.9}.badge{display:inline-block;background:rgba(255,255,255,.15);padding:7px 12px;border-radius:20px;font-size:13px;margin-top:14px}.panel{background:#fff;border-radius:15px;padding:20px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:20px}.filters-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.filters label{font-size:12px;font-weight:700;display:block;margin-bottom:5px}.filters input,.filters select{width:100%;box-sizing:border-box;padding:10px;border:1px solid #d7dde3;border-radius:8px;background:#fff}.actions{display:flex;gap:10px;align-items:end}.btn{display:inline-block;padding:10px 16px;border-radius:8px;text-decoration:none;border:0;cursor:pointer;font-weight:600}.btn-primary{background:#176b87;color:#fff}.btn-light{background:#eef2f5;color:#263238}.btn-report{background:#0f3d56;color:#fff}.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}.kpi{background:#fff;border-radius:15px;padding:20px;box-shadow:0 4px 18px rgba(0,0,0,.06);border-top:4px solid #176b87}.kpi span{color:#667085;font-size:13px}.kpi strong{display:block;font-size:30px;margin-top:6px;color:#0f3d56}.kpi small{display:block;color:#667085;margin-top:5px}.section-head{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:14px}.section-head h2{margin:0;color:#0f3d56}.question{background:#fff;border-radius:15px;padding:22px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:20px}.question h2{font-size:19px;margin:0;color:#0f3d56}.question-meta{color:#667085;font-size:13px;margin:7px 0 15px}.option{margin:14px 0}.option-line{display:flex;justify-content:space-between;gap:15px;margin-bottom:6px}.bar{height:10px;background:#e7ebef;border-radius:10px;overflow:hidden}.fill{height:100%;background:#176b87;border-radius:10px}.chart-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}.chart-card{border:1px solid #e5e7eb;border-radius:12px;padding:16px}.chart-card h3{margin:0 0 14px;font-size:15px;color:#334155}.donut-wrap{display:flex;align-items:center;justify-content:center;min-height:190px}.donut{width:155px;height:155px;border-radius:50%;background:conic-gradient(#176b87 0deg {{ min(360, ($summary['favorable_experience'] ?? 0) * 3.6) }}deg,#e7ebef {{ min(360, ($summary['favorable_experience'] ?? 0) * 3.6) }}deg 360deg);position:relative}.donut:after{content:'';position:absolute;inset:35px;background:#fff;border-radius:50%}.donut-text{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:1;font-size:22px;font-weight:800;color:#0f3d56}.matrix{width:100%;border-collapse:collapse;margin-top:15px}.matrix th,.matrix td{padding:10px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}.matrix th{background:#f8fafc;color:#334155}.avg{font-weight:800;color:#0f3d56;white-space:nowrap}.suggestion{border-left:4px solid #176b87;background:#f8fafb;padding:12px 15px;margin:10px 0;border-radius:0 8px 8px 0}.empty{padding:20px;text-align:center;color:#667085;background:#f8fafb;border-radius:10px}.muted{color:#667085}.top-actions{display:flex;gap:10px;flex-wrap:wrap}.note{background:#eef7f9;border-left:4px solid #176b87;padding:12px 15px;border-radius:7px;color:#334155;margin-bottom:20px}@media(max-width:1100px){.filters-grid{grid-template-columns:repeat(2,1fr)}.kpi-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:700px){.admin-wrap{padding:10px}.hero-row,.section-head{align-items:flex-start;flex-direction:column}.filters-grid,.kpi-grid,.chart-grid{grid-template-columns:1fr}.hero h1{font-size:24px}.matrix{font-size:13px;display:block;overflow-x:auto}}
</style>

<div class="admin-wrap">
    <div class="hero">
        <div class="hero-row">
            <div>
                <h1>Dashboard de Satisfacción del Usuario</h1>
                <p>{{ $survey->name }}</p>
                <span class="badge">Panel administrativo · Resultados filtrados</span>
            </div>
            <div class="top-actions">
                <a class="btn btn-light" href="{{ route('home') }}">Inicio</a>
                <a class="btn btn-report" href="#informe">📄 Informe</a>
            </div>
        </div>
    </div>

    <form method="GET" class="panel filters">
        <div class="section-head"><h2>Filtros de resultados</h2><span class="muted">Los indicadores y gráficas se actualizan con estos filtros.</span></div>
        <div class="filters-grid">
            <div><label>Fecha desde</label><input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"></div>
            <div><label>Fecha hasta</label><input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"></div>
            <div><label>Centro de atención</label><select name="care_center"><option value="">Todos</option>@foreach($filterValues['care_centers'] as $value)<option value="{{ $value }}" @selected(($filters['care_center'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>EPS</label><select name="eps"><option value="">Todas</option>@foreach($filterValues['eps_list'] as $value)<option value="{{ $value }}" @selected(($filters['eps'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>Régimen</label><select name="regime"><option value="">Todos</option>@foreach($filterValues['regimes'] as $value)<option value="{{ $value }}" @selected(($filters['regime'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>Género</label><select name="gender"><option value="">Todos</option>@foreach($filterValues['genders'] as $value)<option value="{{ $value }}" @selected(($filters['gender'] ?? '') === $value)>{{ $value }}</option>@endforeach</select></div>
            <div><label>Edad desde</label><input type="number" min="0" max="120" name="age_from" value="{{ $filters['age_from'] ?? '' }}" placeholder="Ej. 18"></div>
            <div><label>Edad hasta</label><input type="number" min="0" max="120" name="age_to" value="{{ $filters['age_to'] ?? '' }}" placeholder="Ej. 60"></div>
        </div>
        <div class="actions" style="margin-top:14px"><button class="btn btn-primary" type="submit">Aplicar filtros</button><a class="btn btn-light" href="{{ route('admin.surveys.results', $survey->slug) }}">Limpiar</a></div>
    </form>

    <div class="kpi-grid">
        <div class="kpi"><span>Total de encuestas</span><strong>{{ $summary['total'] }}</strong><small>Respuestas según filtros</small></div>
        <div class="kpi"><span>Experiencia favorable</span><strong>{{ $summary['favorable_experience'] }}%</strong><small>Muy Buena + Buena</small></div>
        <div class="kpi"><span>Limpieza y desinfección</span><strong>{{ $summary['cleaning_average'] !== null ? $summary['cleaning_average'].' / 5' : '—' }}</strong><small>Promedio de calificación</small></div>
        <div class="kpi"><span>Servicios evaluados</span><strong>{{ $summary['services_average'] !== null ? $summary['services_average'].' / 5' : '—' }}</strong><small>Promedio general de la matriz</small></div>
    </div>

    <div class="panel">
        <div class="section-head"><h2>Resumen visual</h2><span class="muted">Indicadores principales</span></div>
        <div class="chart-grid">
            <div class="chart-card">
                <h3>Experiencia global favorable</h3>
                <div class="donut-wrap"><div class="donut"><div class="donut-text">{{ $summary['favorable_experience'] }}%</div></div></div>
                <p class="muted" style="text-align:center;margin-bottom:0">Respuestas «Muy Buena» y «Buena»</p>
            </div>
            <div class="chart-card">
                <h3>Promedios de satisfacción</h3>
                <div style="margin:22px 0"><div class="option-line"><span>Servicios</span><strong>{{ $summary['services_average'] !== null ? $summary['services_average'].' / 5' : '—' }}</strong></div><div class="bar"><div class="fill" style="width:{{ $summary['services_average'] !== null ? min(100, $summary['services_average'] * 20) : 0 }}%"></div></div></div>
                <div style="margin:22px 0"><div class="option-line"><span>Limpieza</span><strong>{{ $summary['cleaning_average'] !== null ? $summary['cleaning_average'].' / 5' : '—' }}</strong></div><div class="bar"><div class="fill" style="width:{{ $summary['cleaning_average'] !== null ? min(100, $summary['cleaning_average'] * 20) : 0 }}%"></div></div></div>
            </div>
        </div>
    </div>

    @foreach($questionStats as $stat)
        <section class="question">
            <div class="section-head"><div><h2>{{ $stat['question']->position }}. {{ $stat['question']->question_text }}</h2><div class="question-meta">{{ $stat['total'] }} respuestas registradas @if($stat['average'] !== null) · Promedio {{ $stat['average'] }} / 5 @endif</div></div></div>

            @if($stat['question']->type === 'matrix')
                <table class="matrix">
                    <thead><tr><th>Servicio</th><th>Promedio</th><th>Respuestas</th><th>Distribución</th></tr></thead>
                    <tbody>
                    @foreach($stat['items'] as $item)
                        <tr><td><strong>{{ $item['label'] }}</strong></td><td class="avg">{{ $item['average'] !== null ? $item['average'].' / 5' : '—' }}</td><td>{{ $item['total'] }}</td><td>@foreach($item['options'] as $option)<div style="margin-bottom:7px"><div class="option-line"><small>{{ $option['label'] }}</small><small>{{ $option['count'] }} ({{ $option['percent'] }}%)</small></div><div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div></div>@endforeach</td></tr>
                    @endforeach
                    </tbody>
                </table>
            @elseif($stat['question']->type === 'textarea')
                <div class="note">Las sugerencias registradas se muestran en el apartado de observaciones y recomendaciones.</div>
            @else
                @foreach($stat['options'] as $option)
                    <div class="option"><div class="option-line"><span>{{ $option['label'] }}</span><strong>{{ $option['count'] }} ({{ $option['percent'] }}%)</strong></div><div class="bar"><div class="fill" style="width:{{ $option['percent'] }}%"></div></div></div>
                @endforeach
            @endif
        </section>
    @endforeach

    <section class="panel" id="informe">
        <div class="section-head"><h2>Observaciones y recomendaciones</h2><span class="muted">{{ $suggestions->count() }} comentarios</span></div>
        @forelse($suggestions as $suggestion)
            <div class="suggestion"><small class="muted">{{ $suggestion['date'] }}</small><div>{{ $suggestion['text'] }}</div></div>
        @empty
            <div class="empty">No hay sugerencias para los filtros seleccionados.</div>
        @endforelse
    </section>

    <div class="panel" style="text-align:center">
        <strong>Dashboard administrativo de satisfacción del usuario</strong>
        <p class="muted" style="margin-bottom:0">El botón de generación del informe PDF se habilitará en la siguiente etapa.</p>
    </div>
</div>
@endsection
