# ProyectoPAI – Personal Artificial Intelligence

## Descripción del proyecto

**PAI** es una aplicación web tipo ChatGPT desarrollada como sistema de chat inteligente multiusuario. Permite a los usuarios autenticados mantener conversaciones persistentes con una inteligencia artificial local, generando respuestas en tiempo real mediante streaming.

El sistema funciona completamente en local y offline, ejecutando tanto el backend como el modelo de inteligencia artificial directamente en el ordenador del usuario mediante Docker.

El objetivo del proyecto es simular un sistema SaaS moderno de chat con IA, incluyendo gestión de usuarios, historial de conversaciones, streaming de respuestas y ejecución de un modelo de lenguaje local sin dependencias externas ni servicios cloud.

---

## Objetivos principales

- Implementar un sistema de chat tipo ChatGPT
- Soportar múltiples usuarios con autenticación
- Gestionar conversaciones persistentes
- Integrar un modelo de IA local
- Implementar respuestas en streaming (tiempo real)
- Ejecutar todo el sistema completamente offline
- Ejecutar todo el sistema en entorno portable mediante Docker

---

## Arquitectura del sistema

Frontend (Blade / JavaScript)
        ->
Backend (Laravel API)
        ->
Servicios internos (Chat + IA)
        ->
Modelo de IA local (Ollama + Mistral)
        ->
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
- Ollama (motor de inferencia local)
- Modelo LLM local: Mistral
- Inferencia completamente offline

### Infraestructura
- Docker Desktop
- Docker Compose
- Contenedores para:
  - Laravel App
  - MySQL
  - Ollama

---

## Funcionamiento offline

El sistema está diseñado para funcionar completamente sin conexión a internet.

Todas las operaciones se ejecutan localmente:

- Backend Laravel local
- Base de datos MySQL local
- Modelo de IA almacenado localmente
- Generación de respuestas sin APIs externas
- Comunicación interna mediante Docker Network

El modelo de IA se ejecuta mediante Ollama utilizando un volumen persistente Docker para evitar pérdida del modelo entre reinicios o apagados del sistema.

---

## Inteligencia artificial

El sistema utiliza **Ollama** como motor local, ejecutando el modelo **Mistral** dentro de un contenedor Docker.

Esto permite:

- Ejecución totalmente offline
- Privacidad completa de los datos
- Sin dependencias cloud
- Sin consumo de APIs externas
- Respuestas privadas y locales
- Streaming de tokens en tiempo real
- Baja latencia en entorno local

Laravel se comunica directamente con Ollama mediante HTTP interno:

```php
Http::post('http://ollama:11434/api/generate')
```

---

## Funcionalidades principales

- Registro e inicio de sesión de usuarios
- Sistema de conversaciones persistentes
- Chat con IA tipo ChatGPT
- Streaming de respuestas en tiempo real
- Historial completo de mensajes
- Regeneración de respuestas
- Cancelación de generación de respuesta
- Gestión de múltiples conversaciones por usuario
- Ejecución completamente offline

---

## Ejecución con Docker

El proyecto está preparado para ejecutarse completamente mediante Docker Desktop en Windows.

### 1. Construir y levantar contenedores

```bash
docker compose up --build
```

### 2. Acceder a la aplicación

La aplicación se ejecuta localmente en:

```txt
http://localhost:8000
```

---

## Servicios incluidos

| Servicio    | Descripción                              |
| ------------ | ---------------------------------------- |
| Laravel App | Backend principal del sistema            |
| MySQL       | Base de datos local                      |
| Ollama      | Motor de IA local offline                |

---

## Persistencia del modelo de IA

El modelo Mistral se almacena en un volumen Docker persistente:

```yaml
volumes:
  - ollama_data:/root/.ollama
```

Esto permite:

- Mantener el modelo aunque el contenedor se elimine
- Portabilidad entre ordenadores
- Persistencia tras reinicios
- Ejecución offline estable

---

## Flujo de generación de respuesta

1. El usuario envía un mensaje
2. Laravel guarda el mensaje en MySQL
3. Se construye el contexto de conversación
4. Laravel envía el prompt a Ollama
5. Ollama genera la respuesta token a token
6. El frontend recibe el stream en tiempo real
7. La respuesta se almacena en la base de datos

---

## Seguridad

- Autenticación mediante Laravel Breeze
- Middleware de autenticación obligatorio
- Aislamiento de conversaciones por usuario
- Policies para control de acceso
- Sin envío de datos a servidores externos
- Ejecución completamente local

---

## Requisitos del sistema

### Requisitos mínimos

- Windows 10/11
- Docker Desktop
- 8GB RAM recomendados
- Espacio libre suficiente para modelos LLM

### Requisitos opcionales

- PHP 8.2+
- Composer
- Node.js

---

## Portabilidad del sistema

El sistema puede transportarse fácilmente entre ordenadores mediante:

- Repositorio Git
- Docker Compose
- Volumen persistente de Ollama
- Exportación/importación del modelo local

Esto permite continuar el desarrollo o realizar presentaciones sin necesidad de reinstalar modelos.

---

## Casos de uso

- Asistente virtual local
- Chat privado sin conexión
- Sistema educativo de IA
- Prototipo SaaS de IA
- Laboratorio local de LLMs
- Aplicación IA portable

---

## Posibles mejoras futuras

- Memoria a largo plazo
- Sistema de roles (admin/user)
- Streaming mediante WebSockets
- Interfaz avanzada React/Vue
- Soporte multi-modelo
- Integración RAG local
- Base vectorial local
- GPU acceleration

---

## Autor

Alberto Méndez Núñez - DAW2
