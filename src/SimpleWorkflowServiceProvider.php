<?php

namespace Heloufir\SimpleWorkflow;

use Heloufir\SimpleWorkflow\Commands\WorkflowConfiguration;
use Heloufir\SimpleWorkflow\Commands\WorkflowInstallation;
use Heloufir\SimpleWorkflow\Http\Controllers\ActionController;
use Heloufir\SimpleWorkflow\Http\Controllers\ModuleController;
use Heloufir\SimpleWorkflow\Http\Controllers\StatusController;
use Heloufir\SimpleWorkflow\Http\Controllers\WorkflowController;
use Illuminate\Support\ServiceProvider;

class SimpleWorkflowServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register ActionController
        $this->app->make(ActionController::class);

        // Register StatusController
        $this->app->make(StatusController::class);

        // Register ModuleController
        $this->app->make(ModuleController::class);

        // Register WorkflowController
        $this->app->make(WorkflowController::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Register package commands
        $this->commands([
            WorkflowConfiguration::class,
            WorkflowInstallation::class
        ]);

        // Register package routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');

        // Register package migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Publish package sources
        $this->publishes([
            __DIR__ . '/config/simple-workflow.php' => config_path('simple-workflow.php')
        ]);
    }
}
