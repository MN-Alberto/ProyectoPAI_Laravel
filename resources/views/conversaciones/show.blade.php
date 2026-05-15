@extends('layouts.chat')

@section('content')

    <h1>PAI – Test de conectividad</h1>
    <p>Usuario: {{ auth()->user()->name }}</p>
    <p>Conversación: {{ $conversacion->id }}</p>

    <hr>

    <div id="mensajes">
        @foreach ($mensajes as $mensaje)
            <p><strong>{{ $mensaje->rol }}:</strong> {{ $mensaje->contenido }}</p>
        @endforeach
    </div>

    <hr>

    <textarea id="input" rows="3" cols="50" placeholder="Escribe tu mensaje..."></textarea>
    <br>
    <button id="btn-enviar" onclick="enviar()">Enviar</button>

    <p id="estado"></p>

    <script>
        const CONVERSACION_ID = {{ $conversacion->id }};
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        async function enviar() {
            const input = document.getElementById('input');
            const content = input.value.trim();
            if (!content) return;

            input.value = '';
            input.disabled = true;
            document.getElementById('btn-enviar').disabled = true;
            document.getElementById('estado').innerText = 'Esperando respuesta de Ollama...';

            document.getElementById('mensajes').innerHTML +=
                `<p><strong>user:</strong> ${content}</p>`;

            try {
                const res = await fetch(`/conversaciones/${CONVERSACION_ID}/mensajes`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                    },
                    body: JSON.stringify({ content }),
                });

                const data = await res.json();

                if (data.error) {
                    document.getElementById('estado').innerText = 'ERROR: ' + data.error;
                } else {
                    document.getElementById('mensajes').innerHTML +=
                        `<p><strong>assistant:</strong> ${data.response}</p>`;
                    document.getElementById('estado').innerText = '';
                }

            } catch (err) {
                document.getElementById('estado').innerText = 'Error de conexión: ' + err.message;
            } finally {
                input.disabled = false;
                document.getElementById('btn-enviar').disabled = false;
            }
        }
    </script>

@endsection