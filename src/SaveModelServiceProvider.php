<?php

namespace Asdh\SaveModel;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Asdh\SaveModel\Commands\MakeFieldCommand;

class SaveModelServiceProvider extends PackageServiceProvider
{
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
