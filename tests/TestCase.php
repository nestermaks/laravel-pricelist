<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Astrotomic\Translatable\TranslatableServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Nestermaks\LaravelPricelist\LaravelPricelistServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

//        $this->loadLaravelMigrations();
        $this->setUpDatabase();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Nestermaks\\LaravelPricelist\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelPricelistServiceProvider::class,
            TranslatableServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }

    protected function setUpDatabase()
    {
        include_once __DIR__ . '/../tests/models/TestModel.php';
        include_once __DIR__ . '/../tests/database/migrations/create_test_model_tables.php.stub';
        (new \CreateTestModels())->down();
        (new \CreateTestModels())->up();

        include_once __DIR__ . '/../database/migrations/create_pricelist_tables.php.stub';
        (new \CreatePricelistTables())->down();
        (new \CreatePricelistTables())->up();

        include_once __DIR__ . '/../database/migrations/create_pricelist_translation_tables.php.stub';
        (new \CreatePricelistTranslationTables())->down();
        (new \CreatePricelistTranslationTables())->up();
    }
}
