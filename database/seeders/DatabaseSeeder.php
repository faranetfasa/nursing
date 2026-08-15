<?php

namespace Database\Seeders;

use App\Modules\Core\Database\Seeders\CoreDatabaseSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Only the Core module is seeded in this phase. Every module registers its
     * own seeder here once it is implemented.
     */
    public function run(): void
    {
        $this->call([
            CoreDatabaseSeeder::class,
        ]);
    }
}
