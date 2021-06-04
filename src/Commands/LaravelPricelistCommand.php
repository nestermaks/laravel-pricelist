<?php

namespace Nestermaks\LaravelPricelist\Commands;

use Illuminate\Console\Command;

class LaravelPricelistCommand extends Command
{
    public $signature = 'laravel-pricelist';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
