<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use App\Models\Property;
use App\Observers\PropertyObserver;
use App\Repositories\PropertyRepository;
use App\Services\Search\SearchService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register services — bind interfaces, repos, and services into IoC container.
     */
    public function register(): void
    {
        // Bind PropertyRepository as singleton (one instance per request lifecycle)
        $this->app->singleton(PropertyRepository::class);

        // Bind SearchService to use PropertyRepository (DI injection)
        $this->app->singleton(SearchService::class, function ($app) {
            return new SearchService($app->make(PropertyRepository::class));
        });
    }

    /**
     * Bootstrap application services.
     */
    public function boot(): void
    {
        // ── Register Model Observers ────────────────────────────────────
        Property::observe(PropertyObserver::class);

        // ── Strict Mode in Development (catch N+1, lazy loading, etc.) ─
        if ($this->app->environment('local', 'testing')) {
            Model::preventLazyLoading();          // Throw on N+1
            Model::preventSilentlyDiscardingAttributes(); // Throw on mass-assign miss
        }

        // ── Slow Query Logging (production monitoring) ──────────────────
        if ($this->app->environment('production')) {
            DB::listen(function ($query) {
                if ($query->time > 1000) { // Log queries taking > 1 second
                    Log::warning("SLOW QUERY [{$query->time}ms]: {$query->sql}");
                }
            });
        }

        // ── Pagination — use Stockifly truncated SaaS style ─────────────────────────
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.stockifly');
        \Illuminate\Pagination\Paginator::defaultSimpleView('vendor.pagination.stockifly');
    }
}
