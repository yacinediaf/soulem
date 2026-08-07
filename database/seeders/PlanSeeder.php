<?php

namespace Database\Seeders;

use App\Enums\PlanSlug;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        foreach (PlanSlug::cases() as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan->value],
                ['name' => ucfirst($plan->value)],
            );
        }
    }
}
