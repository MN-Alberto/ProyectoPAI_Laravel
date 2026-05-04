# ProyectoPAI – Personal Artificial Intelligence

## Descripción del proyecto

**ProyectoPAI** es una aplicación web tipo ChatGPT desarrollada como sistema de chat inteligente multiusuario. Permite a los usuarios autenticados mantener conversaciones persistentes (historial de conversaciones con cada usuario) con una inteligencia artificial local, generando respuestas en tiempo real mediante streaming.

El objetivo del proyecto es simular un sistema SaaS moderno de chat con IA, incluyendo gestión de usuarios, historial de conversaciones, streaming de respuestas y ejecución de un modelo de lenguaje local sin dependencias en la nube.

---

## Objetivos principales

- Implementar un sistema de chat tipo ChatGPT
- Soportar múltiples usuarios con autenticación
- Gestionar conversaciones persistentes
- Integrar un modelo de IA local
- Implementar respuestas en streaming (tiempo real)
- Ejecutar todo el sistema en entorno portable mediante Docker

---

## Arquitectura del sistema

Frontend (Blade / JavaScript)
        ↓
Backend (Laravel API)
        ↓
Servicios internos (Chat + IA)
        ↓
Modelo de IA local (Ollama)
        ↓
Base de datos (MySQL)

---

## Tecnologías utilizadas

### Backend
- Laravel (PHP Framework)
- Eloquent ORM
- Laravel Breeze (autenticación)
- Laravel Policies (control de acceso)

### Frontend
- Blade Templates
- JavaScript (fetch + streaming SSE)
- UI tipo chat en tiempo real

### Base de datos
- MySQL

### Inteligencia Artificial
- Ollama (ejecución local de modelos LLM)
- Modelo: Mistral

### Infraestructura
- Docker
- Docker Compose
- Contenedores para:
  - Laravel App
  - MySQL
  - Ollama

---

## Inteligencia artificial

El sistema utiliza **Ollama** como motor de inferencia local, lo que permite:

- Ejecución de modelos sin conexión a internet
- Respuestas privadas (sin APIs externas)
- Streaming de tokens en tiempo real
- Baja latencia en entorno local

---

## Funcionalidades principales

- Registro e inicio de sesión de usuarios
- Sistema de conversaciones persistentes
- Chat con IA tipo ChatGPT
- Streaming de respuestas en tiempo real
- Historial completo de mensajes
- Regeneración de respuestas
- Pausar generación (cancelación de respuesta)
- Gestión de múltiples conversaciones por usuario

---

## Ejecución con Docker

El proyecto está preparado para ejecutarse completamente en Docker.

### 1. Construir y levantar contenedores

```bash
docker compose up --build
```

### 2. Acceder a la aplicación

La aplicación se aloja localmente en el puerto 8000.

```
http://localhost:8000
```

---

## Servicios incluidos

| Servicio    | Descripción                   |
| ----------- | ----------------------------- |
| App Laravel | Backend principal del sistema |
| MySQL       | Base de datos MySQL           |
| Ollama      | Motor de IA local             |

---

## Flujo de generación de respuesta

1. El usuario envía un mensaje
2. Se guarda en la base de datos
3. Se construye el contexto de conversación
4. Laravel envía el prompt a Ollama
5. Ollama genera respuesta token a token
6. El frontend recibe el stream en tiempo real
7. La respuesta se guarda en base de datos

---

## Seguridad

- Autenticación mediante Laravel Breeze
- Aislamiento de conversaciones por usuario
- Policies para control de acceso a recursos
- Middleware de autenticación obligatorio

---

## Requisitos del sistema

- Docker Desktop
- PHP 8.2+ (si se ejecuta sin Docker)
- Composer (opcional fuera de Docker)
- Node.js (para modificar el frontend)

---

## Casos de uso

- Asistente virtual local
- Sistema educativo de IA
- Chat interno privado
- Prototipo de SaaS de IA

---

## Posibles mejoras futuras

- Memoria a largo plazo del modelo
- Sistema de roles (admin / user)
- Streaming con WebSockets
- Interfaz tipo ChatGPT avanzada (React/Vue)
- Soporte multi-modelo (switch de LLMs)

---

## Autor

Alberto Méndez Núñez - DAW2
