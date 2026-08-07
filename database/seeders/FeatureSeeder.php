<?php

namespace Database\Seeders;

use App\Enums\FeatureKey;
use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        foreach (FeatureKey::cases() as $feature) {
            Feature::updateOrCreate(
                ['key' => $feature->value],
                ['name' => ucfirst($feature->value)],
            );
        }
    }
}
