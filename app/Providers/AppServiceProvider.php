<?php

namespace App\Providers;

use App\AI\AIService;
use App\AI\ConnectivityChecker\ConnectivityChecker;
use App\AI\Providers\ExternalAIProvider;
use App\AI\Providers\OllamaProvider;
use App\Chat\ChatService;
use App\Chat\InputGuardrails;
use App\Chat\MissingInfoPolicy;
use App\Chat\Orchestrator\ChatOrchestrator;
use App\Chat\PostProcessor;
use App\Chat\QueryExtractor;
use App\Models\Experience;
use App\Observers\ExperienceObserver;
use App\RAG\Retriever;
use App\RAG\SimilarityCalculator;
use App\RAG\SufficiencyEvaluator;
use App\Search\SearchApiProvider;
use App\Search\WebSearchProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIService::class, function ($app) {
            $ollamaConfig = config('ai.ollama');
            $externalConfig = config('ai.external');

            $ollamaProvider = new OllamaProvider(
                $ollamaConfig['base_url'],
                $ollamaConfig['chat_model'],
                $ollamaConfig['embed_model'],
                $ollamaConfig['timeout']
            );

            $externalProvider = null;
            if (!empty($externalConfig['base_url']) && !empty($externalConfig['api_key']) && !empty($externalConfig['model'])) {
                $externalProvider = new ExternalAIProvider(
                    $externalConfig['base_url'],
                    $externalConfig['api_key'],
                    $externalConfig['model'],
                    $externalConfig['timeout']
                );
            }

            return new AIService($ollamaProvider, $externalProvider);
        });

        $this->app->singleton(QueryExtractor::class, function ($app) {
            return new QueryExtractor($app->make(AIService::class));
        });

        $this->app->singleton(MissingInfoPolicy::class, function () {
            return MissingInfoPolicy::fromConfig();
        });

        $this->app->singleton(ConnectivityChecker::class, function () {
            return ConnectivityChecker::fromConfig();
        });

        $this->app->singleton(SimilarityCalculator::class, function () {
            return new SimilarityCalculator();
        });

        $this->app->singleton(Retriever::class, function ($app) {
            return new Retriever(
                $app->make(AIService::class),
                $app->make(SimilarityCalculator::class),
            );
        });

        $this->app->singleton(SufficiencyEvaluator::class, function () {
            return SufficiencyEvaluator::fromConfig();
        });

        $this->app->singleton(InputGuardrails::class, function () {
            return new InputGuardrails(
                maxLength: config('chat.max_message_length', 1000),
            );
        });

        $this->app->singleton(PostProcessor::class, function () {
            return new PostProcessor();
        });

        $this->app->singleton(ChatOrchestrator::class, function ($app) {
            return new ChatOrchestrator(
                $app->make(AIService::class),
                $app->make(ConnectivityChecker::class),
                $app->make(QueryExtractor::class),
                $app->make(MissingInfoPolicy::class),
                $app->make(Retriever::class),
                $app->make(SufficiencyEvaluator::class),
            );
        });

        $this->app->singleton(ChatService::class, function ($app) {
            return new ChatService(
                $app->make(ChatOrchestrator::class),
            );
        });

        $this->app->bind(WebSearchProvider::class, function () {
            $searchConfig = config('ai.search');

            \Illuminate\Support\Facades\Log::info('WebSearchProvider binding resolved', [
                'base_url' => $searchConfig['base_url'] ?? 'not set',
                'api_key' => $searchConfig['api_key'] ?? 'not set',
                'has_base_url' => !empty($searchConfig['base_url']),
                'has_api_key' => !empty($searchConfig['api_key']),
            ]);

            if (empty($searchConfig['base_url']) || empty($searchConfig['api_key'])) {
                return null;
            }

            return new SearchApiProvider(
                $searchConfig['base_url'],
                $searchConfig['api_key'],
                $searchConfig['timeout']
            );
        });
    }

    public function boot(): void
    {
        Experience::observe(ExperienceObserver::class);

        // Directiva Blade personalizada para el script JS de Vite (se ejecuta en runtime)
        Blade::directive('viteJs', function () {
            return "<?php \$manifestPath = public_path('build/manifest.json'); if (file_exists(\$manifestPath)) { \$manifest = json_decode(file_get_contents(\$manifestPath), true); \$jsEntry = \$manifest['resources/js/app.js']['file'] ?? null; if (\$jsEntry) { echo '<script type=\"module\" src=\"' . asset('build/' . \$jsEntry) . '\"></script>'; } } ?>";
        });
    }
}