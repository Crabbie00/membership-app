<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Add any seeders you want to run by default:
        $this->call([
            AddressTypeSeeder::class,
        ]);
    }
}
