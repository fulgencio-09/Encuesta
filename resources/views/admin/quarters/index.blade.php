@extends('layouts.app')

@section('content')
<style>
    .wrap{max-width:1100px;margin:0 auto}.card{background:#fff;border-radius:14px;padding:24px;box-shadow:0 4px 18px rgba(0,0,0,.06);margin-bottom:20px}.grid{display:grid;grid-template-columns:1fr 1fr auto;gap:12px;align-items:center}.field label{display:block;font-weight:700;font-size:13px;margin-bottom:6px}.field input,.field select{width:100%;padding:11px;border:1px solid #d7dde3;border-radius:8px;height:44px}.btn{border:0;border-radius:8px;padding:11px 16px;cursor:pointer;font-weight:700;white-space:nowrap;text-decoration:none;display:inline-flex;align-items:center;justify-content:center;height:44px}.primary{background:#176b87;color:#fff}.secondary{background:#eef2f5;color:#344054;border:1px solid #d7dde3}.table{width:100%;border-collapse:collapse}.table th,.table td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left}.status{font-weight:700}.active{color:#16803c}.inactive{color:#b42318}.small{font-size:13px;color:#667085}.alert{padding:12px;border-radius:8px;background:#eaf7ee;color:#176b3a;margin-bottom:15px}.header-actions{display:flex;justify-content:space-between;align-items:center;gap:15px;margin-bottom:8px}.header-actions h1{margin:0}@media(max-width:700px){.grid{grid-template-columns:1fr}.table{font-size:13px}.header-actions{align-items:flex-start;flex-direction:column}.header-actions .btn{width:100%}}
</style>
<div class="wrap">
    <div class="card">
        <div class="header-actions">
            <h1>Parametrización de trimestres</h1>
            <a href="{{ route('home') }}" class="btn secondary">← Regresar al Dashboard</a>
        </div>
        <p class="small">Cree los periodos que estarán disponibles para consultar en el Dashboard. Los trimestres inactivos no se mostrarán como periodos disponibles.</p>
        @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert" style="background:#fff1f0;color:#b42318">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('admin.quarters.store') }}" class="grid">
            @csrf
            <div class="field"><label>Año</label><input type="number" name="year" min="2000" max="2100" value="{{ old('year', now()->year) }}" required></div>
            <div class="field"><label>Trimestre</label><select name="quarter" required><option value="1">Primer Trimestre (enero - marzo)</option><option value="2">Segundo Trimestre (abril - junio)</option><option value="3">Tercer Trimestre (julio - septiembre)</option><option value="4">Cuarto Trimestre (octubre - diciembre)</option></select></div>
            <button class="btn primary" type="submit">Crear trimestre</button>
        </form>
    </div>

    <div class="card">
        <h2 style="margin-top:0">Trimestres configurados</h2>
        <table class="table">
            <thead><tr><th>Periodo</th><th>Desde</th><th>Hasta</th><th>Estado</th><th>Acción</th></tr></thead>
            <tbody>
            @forelse($quarters as $quarter)
                <tr>
                    <td><strong>{{ $quarter->name }}</strong></td>
                    <td>{{ $quarter->start_date->format('d/m/Y') }}</td>
                    <td>{{ $quarter->end_date->format('d/m/Y') }}</td>
                    <td class="status {{ $quarter->is_active ? 'active' : 'inactive' }}">{{ $quarter->is_active ? 'Activo' : 'Inactivo' }}</td>
                    <td><form method="POST" action="{{ route('admin.quarters.toggle', $quarter) }}">@csrf @method('PATCH')<button class="btn" type="submit">{{ $quarter->is_active ? 'Desactivar' : 'Activar' }}</button></form></td>
                </tr>
            @empty
                <tr><td colspan="5" class="small">No hay trimestres configurados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
