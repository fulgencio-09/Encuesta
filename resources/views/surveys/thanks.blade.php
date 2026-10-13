<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Encuesta enviada</title>
    <style>
        *{box-sizing:border-box}body{margin:0;font-family:Arial,sans-serif;background:#f4f7f9;color:#1f2937;display:flex;min-height:100vh;align-items:center;justify-content:center}.card{background:#fff;max-width:620px;margin:20px;padding:45px 35px;border-radius:16px;text-align:center;box-shadow:0 8px 30px rgba(0,0,0,.08)}.icon{font-size:55px;margin-bottom:15px}.title{color:#0f4c5c;font-size:27px;margin:0 0 15px}.text{line-height:1.7;color:#4b5563}.btn{display:inline-block;margin-top:20px;background:#0f4c5c;color:#fff;text-decoration:none;padding:12px 22px;border-radius:8px}
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✓</div>
        <h1 class="title">¡Gracias por su colaboración!</h1>
        <p class="text">Su encuesta de satisfacción ha sido registrada correctamente.</p>
        <p class="text">Sus respuestas y recomendaciones son importantes para la mejora de los servicios de la ESE Hospital Santa Teresa de Jesús de Ávila.</p>
        <a class="btn" href="{{ route('survey.show', $survey->slug) }}">Diligenciar otra encuesta</a>
    </div>
</body>
</html>
