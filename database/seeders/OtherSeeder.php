<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Other;

class OtherSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan selalu ada 1 record (singleton pattern)
        Other::firstOrCreate(
            ['id' => 1],
            [
                'about'            => null,
                'privacy_policy'   => null,
                'terms_conditions' => null,
            ]
        );
    }
}
