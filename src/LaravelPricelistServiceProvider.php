<?php

namespace Nestermaks\LaravelPricelist;

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
            ->hasTranslations()
            ->hasConfigFile('pricelist')
            ->hasRoute('api')
            ->hasMigrations([
                'create_pricelist_tables',
                'create_pricelist_translation_tables',
            ])
        ;
    }

    public function packageRegistered()
    {
        parent::packageRegistered();

        config()->set('translatable.locales', config('pricelist.locales'));
    }
}
