# AGENTS.md - Comunidad Láser Chatbot IA

## PROYECTO
Prototipo del chatbot de IA de "Comunidad Láser", una plataforma colaborativa donde usuarios comparten experiencias de grabado láser (material, espesor, tipo de trabajo, máquina, potencia, software, parámetros, resultado, observaciones). Este prototipo SOLO implementa la parte de IA, con experiencias de ejemplo cargadas por seeders.

## STACK
- PHP 8.3+
- Laravel (última estable)
- MySQL
- Livewire + Blade para la UI de chat
- Ollama para IA local
- Una API externa configurable
- Pruebas con Pest o PHPUnit

## REGLAS DE ARQUITECTURA

1. **Prioridad de fuentes**: (1) experiencias de la base de datos vía RAG, (2) API externa si la info es insuficiente y hay Internet, (3) Ollama si no hay Internet, (4) avisar que no hay información suficiente. **NUNCA inventar parámetros de grabado.**

2. **Separación de responsabilidades**: La IA nunca accede directo a la base de datos. Laravel recupera los datos y le pasa solo el contexto necesario.

3. **Capa AIService**: Toda la IA pasa por una capa `AIService` con proveedores intercambiables (`OllamaProvider` y `ExternalAIProvider`) elegidos por configuración (`AI_PROVIDER`). Ninguna otra clase habla con un proveedor concreto.

4. **Embeddings locales**: Los embeddings siempre se generan con Ollama (local), tanto para experiencias como para consultas.

5. **Respuestas orientativas**: Las respuestas con parámetros son orientativas: nunca valores garantizados, siempre rangos o referencias a experiencias de la comunidad, con advertencia.

6. **Configuración externa**: Todo valor configurable (umbrales, modelos, URLs, timeouts, límites) va en `config/` y `.env`, nunca hardcodeado.

7. **Idiomas**: Código en inglés, textos al usuario y prompts del sistema en español rioplatense neutro.

8. **Principios SOLID**: Cada clase con una sola responsabilidad; inyección de dependencias; sin lógica de negocio en controladores ni componentes Livewire.

9. **Testeabilidad**: Todo lo que llame a Ollama o a la API externa debe poder simularse en tests con `Http::fake()`.

10. **Seguridad**: Nunca commitear claves. Mantener `.env.example` actualizado.

## FLUJO DE TRABAJO
Al terminar cada módulo:
1. Correr los tests
2. Listar los archivos creados o modificados
3. Actualizar `README.md` con lo agregado