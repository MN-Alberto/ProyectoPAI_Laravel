<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Restablecer Contraseña - PAI</title>
    <style>
        /*
        // Estilos CSS importados desde el archivo restablecer.css porque no soporta @import
        */
        {!! file_get_contents(resource_path('css/emails/restablecer.css')) !!}
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('images/logoPAI.png')))
                <img src="{{ $message->embed(public_path('images/logoPAI.png')) }}" alt="PAI Logo">
            @endif
            <div class="header-title">PAI - Inteligencia Artificial</div>
        </div>
        <div class="content">
            <h1>Hola, {{ $user->name }}</h1>
            <p>Recibiste este correo porque solicitaste restablecer la contraseña para tu cuenta de PAI.</p>
            <div class="btn-container">
                <a href="{{ $url }}" class="btn" target="_blank">Restablecer contraseña</a>
            </div>
            <p>Este enlace de restablecimiento de contraseña caducará en 60 minutos.</p>
            <p>Si no realizaste esta solicitud, puedes ignorar este correo de forma segura; tu contraseña seguirá siendo
                la misma.</p>
            <div class="divider"></div>
            <div class="footer">
                Si tienes problemas para hacer clic en el botón "Restablecer contraseña", copia y pega la siguiente URL
                en tu navegador web:
                <br>
                <a href="{{ $url }}">{{ $url }}</a>
                <div class="footer-text">
                    Este es un correo automático, por favor no respondas a este mensaje.
                </div>
            </div>
        </div>
    </div>
</body>

</html>