<?php

namespace Nestermaks\LaravelPricelist;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Nestermaks\LaravelPricelist\Commands\LaravelPricelistCommand;

class LaravelPricelistServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pricelist')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-pricelist_table')
            ->hasCommand(LaravelPricelistCommand::class);
    }
}
