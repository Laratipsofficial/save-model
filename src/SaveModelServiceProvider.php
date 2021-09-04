<?php

namespace Asdh\SaveModel;

use Asdh\SaveModel\Commands\MakeFieldCommand;
use Asdh\SaveModel\Commands\SaveModelConfigPublishCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SaveModelServiceProvider extends PackageServiceProvider
{

    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/save_model.php' => config_path('save_model.php'),
            ], 'savemodel-config');
            // Registering package commands.
            $this->commands([
                SaveModelConfigPublishCommand::class,
            ]);
        }
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('save-model')
            ->hasConfigFile('save_model')
            ->hasCommand(MakeFieldCommand::class);
    }
}
