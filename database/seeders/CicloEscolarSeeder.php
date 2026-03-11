<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CicloEscolar;

class CicloEscolarSeeder extends Seeder
{
    public function run(): void
    {
        CicloEscolar::firstOrCreate(
            ['nombre' => '2025-2'],
            ['fecha_inicio' => '2025-09-01', 'fecha_fin' => '2025-12-31']
        );

        CicloEscolar::firstOrCreate(
            ['nombre' => '2026-1'],
            ['fecha_inicio' => '2026-01-06', 'fecha_fin' => '2026-04-30']
        );
    }
}
