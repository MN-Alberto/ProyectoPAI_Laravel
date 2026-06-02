# ProyectoPAI – Personal Artificial Intelligence

## Descripción del proyecto

**PAI** es una aplicación web tipo ChatGPT desarrollada como sistema de chat inteligente multiusuario. Permite a los usuarios autenticados mantener conversaciones persistentes con una inteligencia artificial, generando respuestas en tiempo real mediante streaming.

El sistema puede funcionar completamente offline si se ejecuta localmente, ejecutando tanto el backend como el modelo de inteligencia artificial directamente en el ordenador del usuario mediante Docker.
También se puede ejecutar de forma online desplegándolo en un servidor web. En ese caso, el modelo de IA se ejecutará en el servidor con Ollama.

El objetivo del proyecto es simular un sistema SaaS moderno de chat con IA, incluyendo gestión de usuarios, historial de conversaciones, streaming de respuestas y ejecución de un modelo de lenguaje sin dependencias externas ni servicios cloud.

---

## Objetivos principales

- Implementar un sistema de chat tipo ChatGPT
- Soportar múltiples usuarios con autenticación
- Gestionar conversaciones persistentes
- Integrar uno o varios modelos de IA en local
- Implementar respuestas en streaming (tiempo real)
- Posibilidad de ejecutar todo el sistema completamente offline
- Posibilidad de ejecutar todo el sistema en entorno portable mediante Docker
- Subir el proyecto a un servidor web y que funcione igual de bien, con el modelo de IA en el servidor con Ollama, no en local.

---

## Arquitectura del sistema

Frontend (Blade / JavaScript) - UI tipo chat en tiempo real
        ->
Nginx (proxy inverso) - HTTPS - Let's Encrypt - DuckDNS
        ->
Backend (Laravel API) - Rutas - controladores - auth - prompt
        ->
Servicios internos (Chat + IA) - construcción del prompt - historial
        ->
Modelos de IA local (Ollama) - Mistral - Phi-3 - DeepSeek - TinyLlama
        ->
Base de datos (MySQL) - Usuarios - conversaciones - mensajes

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
- Modelos LLM locales: Mistral, Phi-3 Mini, DeepSeek Coder, TinyLlama
- Inferencia completamente offline

### Infraestructura
- Docker Desktop
- Docker Compose
- Contenedores para:
  - Laravel App
  - MySQL
  - Ollama

---

## Funcionamiento

La aplicación ahora se encuentra desplegado en un servidor web.

Cuando la aplicación se ejecutaba en local, estaba diseñada para funcionar completamente sin conexión a internet. En ese caso:

Todas las operaciones se ejecutaban localmente:

- Backend Laravel local
- Base de datos MySQL local
- Modelos de IA almacenados localmente
- Generación de respuestas sin APIs externas
- Comunicación interna mediante Docker Network

Los modelos de IA se ejecutan mediante Ollama utilizando un volumen persistente Docker para evitar pérdida del modelo entre reinicios o apagados del sistema.
Se cargarán en memoria un máximo de 2 modelos a la vez. En caso de necesitar otro, Ollama utiliza una política llamada LRU (Last Recently Used), que significa que eliminará el modelo que menos se haya utilizado para liberar memoria y dejar espacio para el nuevo modelo.

---

## Inteligencia artificial

El sistema utiliza **Ollama** como motor local, ejecutando varios modelos dentro de un contenedor Docker.
Los modelos actuales son: Mistral, Phi-3 Mini, DeepSeek Coder, TinyLlama.

Esto permite:

- Ejecución totalmente offline
- Privacidad completa de los datos
- Sin dependencias cloud
- Sin consumo de APIs externas
- Respuestas privadas y locales
- Streaming de tokens en tiempo real
- Baja latencia en entorno local

El navegador envia el mensaje a Laravel, este construye el prompt y lo envia de nuevo al navegador, el navegador se comunica con ollama para enviar el mensaje y recibir la respuesta de ollama.

Ahora se encuentra desplegado en un servidor web, por lo que el navegador se comunica con Laravel a traves de Nginx y este con Ollama.

---

## Funcionalidades principales

- Registro e inicio de sesión de usuarios
- Sistema de conversaciones persistentes
- Chat con IA tipo ChatGPT
- Multi-modelo: selección del modelo a utilizar en cada mensaje individual
- Streaming de respuestas en tiempo real
- Historial completo de mensajes
- Regeneración de respuestas
- Cancelación de generación de respuesta
- Gestión de múltiples conversaciones por usuario
- Ejecución completamente offline
- Vista de administrador para la gestión de usuarios

---

## Ejecución con Docker

El proyecto está preparado para ejecutarse completamente mediante Docker Desktop en Windows.

### 1. Construir y levantar contenedores

```bash
docker compose up --build
```

### 2. Acceder a la aplicación

La aplicación se ejecutaba localmente en:

```txt
http://localhost:8000
```

Ahora se encuentra desplegado en un servidor web:

https://proyectopai.duckdns.org

---

## Servicios incluidos

| Servicio    | Descripción                   |
| ----------- | ----------------------------- |
| Nginx       | Proxy inverso - HTTPS         |
| Laravel App | Backend principal del sistema |
| MySQL       | Base de datos local           |
| Ollama      | Motor de IA local offline     |

---

## Persistencia del modelo de IA

Los modelos se almacenan en un volumen Docker persistente:

```yaml
volumes:
  - ollama_data:/root/.ollama
```

Esto permite:

- Mantener los modelos aunque el contenedor se elimine
- Portabilidad entre ordenadores
- Persistencia tras reinicios
- Ejecución offline estable

---

## Flujo de generación de respuesta

1. El usuario envía un mensaje seleccionando el modelo a utilizar
2. Laravel guarda el mensaje en MySQL y construye el contexto de conversación con el modelo seleccionado
3. Laravel envía el prompt al navegador
4. El navegador se comunica con Ollama para enviar el prompt y recibir la respuesta en streaming
5. Ollama genera la respuesta token a token con el modelo seleccionado
6. El frontend muestra la respuesta en tiempo real
7. Laravel almacena la respuesta final en la base de datos

---

## Seguridad

- Autenticación mediante Laravel Breeze
- Middleware de autenticación obligatorio
- Aislamiento de conversaciones por usuario
- Policies para control de acceso
- Sin envío de datos a servidores externos

---

## Requisitos del sistema

### Requisitos mínimos

- Windows 10/11
- Docker Desktop
- 16GB RAM recomendados
- Espacio libre suficiente para modelos LLM (~30GB)

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
- Interfaz avanzada React/Vue
- Integración RAG local
- GPU acceleration

---

## Autor

Alberto Méndez Núñez - DAW2
