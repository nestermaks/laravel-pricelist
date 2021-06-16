<?php

namespace Nestermaks\LaravelPricelist\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;
use Nestermaks\LaravelPricelist\LaravelPricelistServiceProvider;
use PDO;

class TestCase extends Orchestra
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Nestermaks\\LaravelPricelist\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelPricelistServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

    }

    protected function setUpDatabase()
    {
        include_once __DIR__ . '/../database/migrations/create_pricelist_tables.php.stub';
        (new \CreatePricelistTables())->down();
        (new \CreatePricelistTables())->up();
    }
}
