<?php

namespace Database\Seeders;

use App\Models\AddressType;
use Illuminate\Database\Seeder;

class AddressTypeSeeder extends Seeder
{
    public function run(): void
    {
        AddressType::query()->upsert([
            ['name' => 'Residential Address',   'status' => true],
            ['name' => 'Correspondence Address','status' => true],
        ], ['name']);
    }
}
