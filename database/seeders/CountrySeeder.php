<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::create([
            'name' => 'Indonesia'
        ]);
        Country::create([
            'name' => 'Malaysia'
        ]);
        Country::create([
            'name' => 'Singapore'
        ]);
        Country::create([
            'name' => 'Thailand'
        ]);


    }
}
