<?php

namespace Nestermaks\LaravelPricelist;

use Nestermaks\LaravelPricelist\Commands\LaravelPricelistCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasConfigFile('pricelist')
            ->hasRoute('api')
            ->hasMigrations([
                'create_pricelist_tables',
                'create_pricelist_translation_tables'
            ])
            ->hasCommand(LaravelPricelistCommand::class);
    }

    public function packageRegistered()
    {
        parent::packageRegistered();

        config()->set('translatable.locales', config('pricelist.locales'));
    }
}
