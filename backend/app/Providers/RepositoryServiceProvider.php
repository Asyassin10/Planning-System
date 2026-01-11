<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->bind(
            \App\Repositories\RouteRepository::class,
            function ($app) {
                return new \App\Repositories\RouteRepository(
                    $app->make(\App\Models\Route::class)
                );
            }
        );

        $this->app->bind(
            \App\Repositories\ResourceRepository::class,
            function ($app) {
                return new \App\Repositories\ResourceRepository(
                    $app->make(\App\Models\Resource::class)
                );
            }
        );

        $this->app->bind(
            \App\Repositories\PlanningRequestRepository::class,
            function ($app) {
                return new \App\Repositories\PlanningRequestRepository(
                    $app->make(\App\Models\PlanningRequest::class),
                    $app->make(\App\Models\PlanningRequestItem::class)
                );
            }
        );

        $this->app->bind(
            \App\Repositories\OperationalPlanRepository::class,
            function ($app) {
                return new \App\Repositories\OperationalPlanRepository(
                    $app->make(\App\Models\OperationalPlan::class),
                    $app->make(\App\Models\OperationalPlanVersion::class),
                    $app->make(\App\Models\PlanVersionResource::class)
                );
            }
        );

        $this->app->bind(
            \App\Repositories\ExecutionEventRepository::class,
            function ($app) {
                return new \App\Repositories\ExecutionEventRepository(
                    $app->make(\App\Models\ExecutionEvent::class)
                );
            }
        );

        // Register Services
        $this->app->bind(
            \App\Services\RouteService::class,
            function ($app) {
                return new \App\Services\RouteService(
                    $app->make(\App\Repositories\RouteRepository::class)
                );
            }
        );

        $this->app->bind(
            \App\Services\ResourceService::class,
            function ($app) {
                return new \App\Services\ResourceService(
                    $app->make(\App\Repositories\ResourceRepository::class)
                );
            }
        );

        $this->app->bind(
            \App\Services\PlanningRequestService::class,
            function ($app) {
                return new \App\Services\PlanningRequestService(
                    $app->make(\App\Repositories\PlanningRequestRepository::class)
                );
            }
        );

        $this->app->bind(
            \App\Services\OperationalPlanService::class,
            function ($app) {
                return new \App\Services\OperationalPlanService(
                    $app->make(\App\Repositories\OperationalPlanRepository::class)
                );
            }
        );

        $this->app->bind(
            \App\Services\ExecutionEventService::class,
            function ($app) {
                return new \App\Services\ExecutionEventService(
                    $app->make(\App\Repositories\ExecutionEventRepository::class)
                );
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
