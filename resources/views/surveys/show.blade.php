<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $survey->name }}</title>
    <style>
        *{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:#f4f7f9;color:#1f2937}.top{background:#0f4c5c;color:#fff;padding:18px 20px}.top-inner{max-width:1050px;margin:auto;display:flex;align-items:center;gap:20px}.logo{width:105px;height:auto;display:block;background:#fff;border-radius:10px;padding:4px}.brand h1{margin:0;font-size:24px}.brand p{margin:7px 0 0;opacity:.9}.container{max-width:1050px;margin:28px auto;padding:0 18px}.card{background:#fff;border-radius:14px;padding:28px;box-shadow:0 7px 25px rgba(0,0,0,.07);margin-bottom:20px}.section-title{font-size:20px;color:#0f4c5c;border-bottom:2px solid #e5e7eb;padding-bottom:10px;margin:0 0 20px}.intro{line-height:1.6;color:#4b5563}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}.field label{display:block;font-weight:600;margin-bottom:6px}.input,select,textarea{width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:15px}.input:focus,select:focus,textarea:focus{outline:2px solid #99d5df;border-color:#0f4c5c}.question{margin-bottom:28px}.question-title{font-weight:700;margin-bottom:12px;line-height:1.5}.required{color:#b91c1c}.options{display:flex;flex-wrap:wrap;gap:10px}.option{border:1px solid #d1d5db;border-radius:9px;padding:10px 13px;background:#fafafa}.option input{margin-right:7px}.matrix-wrap{overflow-x:auto}.matrix{width:100%;border-collapse:collapse;min-width:760px}.matrix th,.matrix td{border:1px solid #d1d5db;padding:10px;text-align:center}.matrix th{background:#eef5f7}.matrix th:first-child,.matrix td:first-child{text-align:left}.scale{display:flex;gap:12px;flex-wrap:wrap}.scale label{min-width:55px;text-align:center;border:1px solid #d1d5db;padding:10px;border-radius:8px}.scale input{display:block;margin:0 auto 5px}.actions{text-align:center;margin-top:28px}.btn{border:0;border-radius:9px;padding:14px 30px;background:#0f4c5c;color:white;font-size:16px;font-weight:700;cursor:pointer}.btn:hover{background:#0b3d4a}.errors{background:#fee2e2;color:#991b1b;padding:14px 18px;border-radius:9px;margin-bottom:20px}.muted{color:#6b7280;font-size:14px}@media(max-width:700px){.grid{grid-template-columns:1fr}.card{padding:20px}.top-inner{align-items:flex-start}.logo{width:82px}.brand h1{font-size:18px}}
    </style>
</head>
<body>
<div class="top">
    <div class="top-inner">
        <img class="logo" src="{{ asset('images/ese.png') }}" alt="ESE Hospital Santa Teresa de Jesús de Ávila">
        <div class="brand">
            <h1>ESE Hospital Santa Teresa de Jesús de Ávila</h1>
            <p>Encuesta de satisfacción del usuario</p>
        </div>
    </div>
</div>

<main class="container">
    <div class="card">
        <h2 class="section-title">{{ $survey->name }}</h2>
        <p class="intro">{{ $survey->description }}</p>
        <p class="muted">Sus respuestas y recomendaciones son importantes para la mejora de nuestros servicios. La encuesta es confidencial.</p>
    </div>

    @if($errors->any())
        <div class="errors">
            <strong>Por favor revise la encuesta:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('survey.store', $survey->slug) }}">
        @csrf

        <div class="card">
            <h2 class="section-title">Información del usuario</h2>
            <div class="grid">
                <div class="field"><label>Nombres y Apellidos</label><input class="input" type="text" name="respondent_name" value="{{ old('respondent_name') }}"></div>
                <div class="field"><label>Tipo de documento</label><select name="document_type"><option value="">Seleccione</option>@foreach(['T.I','C.C','Tarjeta de Extranjería','Cédula de Extranjería'] as $v)<option value="{{ $v }}" @selected(old('document_type')===$v)>{{ $v }}</option>@endforeach</select></div>
                <div class="field"><label>Número del Documento</label><input class="input" type="text" name="document_number" value="{{ old('document_number') }}"></div>
                <div class="field"><label>Centro de Atención</label><input class="input" type="text" name="care_center" value="{{ old('care_center') }}"></div>
                <div class="field"><label>Fecha</label><input class="input" type="date" name="response_date" value="{{ old('response_date', now()->toDateString()) }}"></div>
                <div class="field"><label>Género</label><select name="gender"><option value="">Seleccione</option>@foreach(['Masculino','Femenino','LGTBIQ+'] as $v)<option value="{{ $v }}" @selected(old('gender')===$v)>{{ $v }}</option>@endforeach</select></div>
                <div class="field"><label>Embarazo</label><select name="pregnancy"><option value="">Seleccione</option>@foreach(['Sí','No','No Aplica'] as $v)<option value="{{ $v }}" @selected(old('pregnancy')===$v)>{{ $v }}</option>@endforeach</select></div>
                <div class="field"><label>Edad</label><input class="input" type="number" name="age" min="0" max="120" value="{{ old('age') }}"></div>
                <div class="field"><label>EPS</label><select name="eps"><option value="">Seleccione</option>@foreach(['Caja Copi','Dusakawi','Nueva EPS','Magisterio','Otra EPS','Particular'] as $v)<option value="{{ $v }}" @selected(old('eps')===$v)>{{ $v }}</option>@endforeach</select></div>
                <div class="field"><label>Régimen</label><select name="regime"><option value="">Seleccione</option>@foreach(['Régimen Subsidiado','Contributivo','Régimen Especial','Particular'] as $v)<option value="{{ $v }}" @selected(old('regime')===$v)>{{ $v }}</option>@endforeach</select></div>
            </div>
        </div>

        <div class="card">
            <h2 class="section-title">Preguntas de satisfacción</h2>
            @foreach($survey->questions as $question)
                <div class="question">
                    <div class="question-title">{{ $question->position }}. {{ $question->question_text }} @if($question->is_required)<span class="required">*</span>@endif</div>
                    @if($question->type === 'single_choice')
                        <div class="options">@foreach($question->options as $option)<label class="option"><input type="radio" name="answers[{{ $question->question_key }}]" value="{{ $option->value }}" @checked(old('answers.'.$question->question_key)===$option->value)> {{ $option->label }}</label>@endforeach</div>
                    @elseif($question->type === 'scale')
                        <div class="scale">@foreach($question->options as $option)<label><input type="radio" name="answers[{{ $question->question_key }}]" value="{{ $option->value }}" @checked(old('answers.'.$question->question_key)===$option->value)>{{ $option->label }}</label>@endforeach</div>
                    @elseif($question->type === 'matrix')
                        <div class="matrix-wrap"><table class="matrix"><thead><tr><th>Servicio</th>@foreach($question->options as $option)<th>{{ $option->label }}</th>@endforeach</tr></thead><tbody>@foreach($question->items as $item)<tr><td>{{ $item->label }}</td>@foreach($question->options as $option)<td><input type="radio" name="answers[{{ $question->question_key }}][{{ $item->value }}]" value="{{ $option->value }}" @checked(old('answers.'.$question->question_key.'.'.$item->value)===$option->value)></td>@endforeach</tr>@endforeach</tbody></table></div>
                    @elseif($question->type === 'textarea')
                        <textarea name="answers[{{ $question->question_key }}]" rows="5" placeholder="Escriba aquí sus sugerencias o recomendaciones...">{{ old('answers.'.$question->question_key) }}</textarea>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="actions"><button class="btn" type="submit">Enviar encuesta</button></div>
    </form>
</main>
</body>
</html>
