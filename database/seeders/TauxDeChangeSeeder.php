<?php

namespace Database\Seeders;

use App\Models\TauxDeChange;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TauxDeChangeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         TauxDeChange::updateOrCreate(
                ['date' => now()->format('Y-m-d')],
                ['usd_cdf' => rand(2500, 2800) + rand(0, 99)/100]
            );
    }
}
