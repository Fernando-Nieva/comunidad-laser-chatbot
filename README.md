# Comunidad Láser - Chatbot IA

Prototipo del chatbot de IA para "Comunidad Láser", una plataforma colaborativa donde usuarios comparten experiencias de grabado láser.

## Stack Tecnológico

- **PHP 8.3+**
- **Laravel 13.x**
- **MySQL 8.0+**
- **Livewire 4.x + Blade** para la UI de chat
- **Ollama** para IA local
- **API externa configurable**
- **Pest** para pruebas

## Requisitos Previos

- PHP 8.3+
- Composer
- MySQL 8.0+ (o Docker)
- Ollama (para IA local) - [Descargar aquí](https://ollama.ai)

## Instalación

### 1. Clonar y instalar dependencias

```bash
composer install
npm install && npm run build
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
php artisan key:generate
```

Edita `.env` con tus credenciales de base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=comunidad_laser_ia
DB_USERNAME=root
DB_PASSWORD=tu_password
```

### 3. Crear la base de datos

```bash
# Opción A: MySQL local
mysql -u root -p -e "CREATE DATABASE comunidad_laser_ia CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Opción B: Docker
docker run -d --name mysql-comunidad-laser -e MYSQL_ROOT_PASSWORD=secret -e MYSQL_DATABASE=comunidad_laser_ia -p 3306:3306 mysql:8.0
```

### 4. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

### 5. Configurar Ollama (para IA local)

```bash
# Instalar modelos recomendados
ollama pull qwen2.5:7b
ollama pull bge-m3
```

### 6. Levantar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

## Endpoints

- `GET /` - Chat UI (Livewire)
- `GET /health` - Health check de la aplicación y base de datos
- `POST /api/chat` - Endpoint del chatbot (JSON: `{ "message": "...", "conversation_uuid?": "..." }` → `{ "reply": "...", "conversation_uuid": "..." }`)
- `GET /debug` - Panel de depuración (solo entorno local)

## Testing

```bash
# Ejecutar todos los tests
./vendor/bin/pest

# Con cobertura
./vendor/bin/pest --coverage
```

## Variables de Entorno Principales

### IA y Ollama
| Variable | Descripción | Default |
|----------|-------------|---------|
| `AI_PROVIDER` | Proveedor de IA (ollama/external) | `ollama` |
| `OLLAMA_BASE_URL` | URL del servidor Ollama | `http://localhost:11434` |
| `OLLAMA_CHAT_MODEL` | Modelo de chat | `qwen2.5:7b` |
| `OLLAMA_EMBED_MODEL` | Modelo de embeddings | `bge-m3` |

### API Externa (opcional)
| Variable | Descripción |
|----------|-------------|
| `EXTERNAL_AI_BASE_URL` | URL base de la API externa |
| `EXTERNAL_AI_API_KEY` | API Key |
| `EXTERNAL_AI_MODEL` | Modelo a usar |
| `EXTERNAL_AI_TIMEOUT` | Timeout en segundos (30) |

### Conectividad
| Variable | Descripción | Default |
|----------|-------------|---------|
| `AI_CONNECTIVITY_CHECK_URL` | URL para verificar conectividad | `http://www.google.com` |
| `AI_CONNECTIVITY_CHECK_TIMEOUT` | Timeout en segundos | `2` |
| `AI_CONNECTIVITY_CACHE_TTL` | TTL cache en segundos | `30` |

### RAG
| Variable | Descripción | Default |
|----------|-------------|---------|
| `RAG_TOP_K` | Resultados a recuperar | `5` |
| `RAG_RELEVANCE_THRESHOLD` | Umbral de relevancia (0-1) | `0.75` |
| `RAG_MIN_RESULTS` | Mínimo resultados para responder | `2` |
| `RAG_FILTER_BY_MATERIAL` | Filtrar por material del contexto | `true` |
| `RAG_FILTER_BY_JOB_TYPE` | Filtrar por tipo de trabajo del contexto | `true` |
| `RAG_REQUIRE_MATERIAL_MATCH` | Requerir coincidencia de material en evaluación | `true` |

### Chat
| Variable | Descripción | Default |
|----------|-------------|---------|
| `CHAT_HISTORY_LIMIT` | Máx. mensajes de historial enviados al modelo | `12` |
| `RAG_MAX_QUESTIONS_IN_ROW` | Máx. preguntas seguidas antes de buscar | `3` |
| `CHAT_MAX_MESSAGE_LENGTH` | Máx. longitud mensaje usuario | `1000` |
| `CHAT_RATE_LIMIT_MAX_ATTEMPTS` | Máx. requests por ventana | `30` |
| `CHAT_RATE_LIMIT_DECAY_MINUTES` | Ventana rate limit (minutos) | `1` |

## Flujo de Arquitectura (Diagrama de Texto)

```
┌─────────────────────────────────────────────────────────────────┐
│                        USUARIO                                   │
│                    Envía mensaje                                 │
└──────────────────────────┬──────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                      INPUT GUARDRAILS                           │
│  • Longitud máxima (1000 chars)                                 │
│  • Prompt injection (12 patrones)                               │
│  • Off-topic (keywords + contexto láser)                        │
└──────────────────────────┬──────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CHAT ORCHESTRATOR                            │
│  1. Guarda mensaje usuario                                      │
│  2. Actualiza QueryContext (QueryExtractor)                     │
│  3. Verifica MissingInfoPolicy                                  │
│     ├─ Falta dato obligatorio → Pregunta + FIN                  │
│     └─ Consulta conceptual → Respuesta directa (sin RAG)        │
└──────────────────────────┬──────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                        RETRIEVER (RAG)                          │
│  • Embedding de consulta (AIService::embed → Ollama)            │
│  • Carga embeddings de experiencias aprobadas                   │
│  • Similitud coseno + filtros (material, job_type)              │
│  • Top K + threshold (0.75 default)                             │
└──────────────────────────┬──────────────────────────────────────┘
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│                    SUFFICIENCY EVALUATOR                        │
│  • min_results (2) con score ≥ threshold (0.75)                 │
│  • Material match si contexto lo tiene                          │
│  • Resultado: SUFFICIENT / INSUFFICIENT                         │
└──────────────────────────┬──────────────────────────────────────┘
                           ▼
              ┌─────────────────────────────────────┐
              ▼                                     ▼
    ┌───────────────────────┐           ┌───────────────────────┐
    │      SUFICIENTE       │           │      INSUFICIENTE     │
    │  Contexto = experiencias│           │  ¿Hay Internet?       │
    │  Proveedor principal    │           │  ┌───┬────┬────┐     │
    │  (online=external,      │           │  │Sí │ No │     │     │
    │   offline=Ollama)       │           │  ▼   ▼     ▼     │     │
    │  source=community       │           │ Ext.  Ollama  None │
    │  fallback=0             │           │  fb=1  fb=2  fb=3  │
    └─────────────────────────┘           └─────────────────────┘
              │                                     │
              ▼                                     ▼
    ┌───────────────────────────────────────────────────────────────┐
    │                    POST PROCESSOR                             │
    │ • Aviso fijo: "⚠️ Orientativas. Probá en retazo."            │
    │ • Etiqueta fuente: Comunidad / IA Externa / IA Local / None   │
    │ • Aclaración IA externa/local: "NO de experiencias comunidad"│
    │ • Advertencias materiales peligrosos (PVC, vinilo, etc.)     │
    └───────────────────────────────────────────────────────────────┘
                           ▼
              ┌─────────────────────────────────────┐
              ▼                                     ▼
    ┌───────────────────────┐           ┌───────────────────────┐
    │     RESPUESTA FINAL   │           │   MENSAJE "SIN INFO"  │
    │  source, fallback,    │           │  fallback_level=3     │
    │  scores, experience_ids│           │  source=none          │
    └─────────────────────────┘           └───────────────────────┘
```

## Comandos Principales

### Chat
```bash
# Simular conversaciones desde archivo JSON
php artisan chat:simulate chat_simulate_example.json
php artisan chat:simulate chat_simulate_example.json --delay=500 --verbose
```

### RAG
```bash
# Generar embeddings (solo nuevos/cambiados)
php artisan rag:embed

# Regenerar todos los embeddings
php artisan rag:embed --fresh

# Buscar experiencias (depuración)
php artisan rag:search "grabar MDF 3mm con láser 10W"
php artisan rag:search "corte acrílico" --material="Acrílico" --job-type="Corte"

# Calibrar umbral de relevancia
php artisan rag:calibrate
php artisan rag:calibrate --min=0.60 --max=0.80 --step=0.02
```

### Conectividad
```bash
# Probar proveedor configurado
php artisan ai:ping
php artisan ai:ping --provider=ollama
php artisan ai:ping --provider=external
```

### Depuración
```bash
# Ver estado de conversaciones (solo local)
# En navegador: http://localhost:8000/debug?conversation_uuid=...
```

### Testing
```bash
./vendor/bin/pest
./vendor/bin/pest --coverage
```

## Configuración de Ollama

```bash
# Instalar Ollama
curl -fsSL https://ollama.ai/install.sh | sh

# Descargar modelos
ollama pull qwen2.5:7b      # Modelo de chat
ollama pull bge-m3          # Modelo de embeddings

# Verificar
ollama list
ollama run qwen2.5:7b "Hola"
```

## Comandos Principales

```bash
# Instalación completa
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate

# Base de datos
php artisan migrate:fresh --seed

# Embeddings (requiere Ollama corriendo)
php artisan rag:embed

# Servidor
php artisan serve

# Tests
./vendor/bin/pest

# Simulación completa
php artisan chat:simulate chat_simulate_example.json --verbose
```

## Variables de Entorno (Resumen)

Ver `.env.example` para lista completa. Variables clave:

```env
# IA
AI_PROVIDER=ollama
OLLAMA_BASE_URL=http://localhost:11434
OLLAMA_CHAT_MODEL=qwen2.5:7b
OLLAMA_EMBED_MODEL=bge-m3

# API Externa (opcional)
EXTERNAL_AI_BASE_URL=
EXTERNAL_AI_API_KEY=
EXTERNAL_AI_MODEL=
EXTERNAL_AI_TIMEOUT=30

# Conectividad
AI_CONNECTIVITY_CHECK_URL=http://www.google.com
AI_CONNECTIVITY_CHECK_TIMEOUT=2
AI_CONNECTIVITY_CACHE_TTL=30

# RAG
RAG_TOP_K=5
RAG_RELEVANCE_THRESHOLD=0.75
RAG_MIN_RESULTS=2
RAG_FILTER_BY_MATERIAL=true
RAG_FILTER_BY_JOB_TYPE=true
RAG_REQUIRE_MATERIAL_MATCH=true

# Chat
CHAT_HISTORY_LIMIT=12
RAG_MAX_QUESTIONS_IN_ROW=3
CHAT_MAX_MESSAGE_LENGTH=1000
CHAT_RATE_LIMIT_MAX_ATTEMPTS=30
CHAT_RATE_LIMIT_DECAY_MINUTES=1
```

## Panel de Depuración (/debug)

Accesible solo en entorno `local`: `http://localhost:8000/debug`

Muestra para cada conversación:
- QueryContext extraído (material, espesor, potencia, tipo, máquina, software)
- Resultados del Retriever con scores
- Decisión del SufficiencyEvaluator (suficiente/insuficiente, razón)
- Proveedor usado (community/external_ai/local_ai/none)
- Nivel de fallback aplicado (0-3) y razón
- Latencias por etapa (si disponible)
- Configuración completa del sistema

## Testing

### Estructura de Tests
- **Unit**: `SimilarityCalculator`, `PostProcessor`, `InputGuardrails`, `SufficiencyEvaluator`, `QueryExtractor`, `MissingInfoPolicy`, `ModelRelationships`, `ExperienceKnowledgeText`
- **Feature**: `ChatApi`, `ConversationHistory`, `QueryExtractor`, `Retriever`, `ConversationHistory`, `HttpFake`, `ChatE2E`

### Cobertura de Escenarios E2E (ChatE2ETest)
1. **Suficiente** → RAG → comunidad
2. **Insuficiente + online** → API externa
3. **Insuficiente + offline** → Ollama
4. **Insuficiente + offline + Ollama caído** → "sin info"
5. **Incompleta** → pregunta → completa → respuesta
6. **Multi-turno** mantiene contexto
7. **Fallo API externa** → fallback a Ollama

### Mocks utilizados
- `Http::fake()` para Ollama y API externa
- `ConnectivityChecker` mockeado para controlar online/offline
- `MissingInfoPolicy` mockeado para aislar lógica de preguntas
- `Retriever`, `SufficiencyEvaluator`, `AIService` mockeados según escenario

## Panel de Depuración

En entorno local: `http://localhost:8000/debug?conversation_uuid=...`

Muestra por conversación:
- QueryContext extraído (material, espesor, potencia, tipo, máquina, software)
- Resultados del Retriever con scores
- Decisión del SufficiencyEvaluator (suficiente/insuficiente, razón)
- Proveedor usado (community/external_ai/local_ai/none)
- Nivel de fallback (0-3) y razón
- Configuración completa del sistema

## Scripts de Simulación

```bash
# Ejecutar conversaciones de ejemplo
php artisan chat:simulate chat_simulate_example.json

# Con más detalle
php artisan chat:simulate chat_simulate_example.json --delay=500 --verbose
```

Genera reporte JSON en `storage/logs/chat_simulate_YYYYMMDD_HHMMSS.json`

## Archivo de Ejemplo para Simulación

Ver `chat_simulate_example.json` en la raíz del proyecto.

## Seguridad

- **Input Guardrails**: Rechaza prompt injection, off-topic, mensajes muy largos
- **Post Processor**: Avisos obligatorios, etiquetas de fuente, advertencias materiales peligrosos
- **Rate Limiting**: 30 req/min por IP+sesión
- **Sin secretos en logs**: Claves API nunca se loguean
- **Materiales peligrosos**: PVC, vinilo, policarbonato, fibra de carbono, cuero cromado, teflón, ABS, poliestireno, epoxy, fórmica → aviso automático

## Licencia

MIT