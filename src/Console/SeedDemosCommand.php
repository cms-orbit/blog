<?php

declare(strict_types=1);

namespace CmsOrbit\Blog\Console;

use Database\Seeders\DemoBlogInstancesSeeder;
use Illuminate\Console\Command;

class SeedDemosCommand extends Command
{
    protected $signature = 'blog:seed-demos';

    protected $description = 'Seed demo blog instances with sample content';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => DemoBlogInstancesSeeder::class]);

        $this->info('Demo blog instances seeded.');

        return self::SUCCESS;
    }
}
