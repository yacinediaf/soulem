<?php

namespace Database\Seeders;

use App\Enums\FeatureKey;
use App\Enums\PlanSlug;
use App\Models\Feature;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanFeatureSeeder extends Seeder
{
    /**
     * @var array<string, FeatureKey[]>
     */
    private const PLAN_FEATURES = [
        PlanSlug::Free->value => [
            FeatureKey::Products,
            FeatureKey::Orders,
        ],
        PlanSlug::Growth->value => [
            FeatureKey::Products,
            FeatureKey::Orders,
            FeatureKey::Inventory,
            FeatureKey::Notifications,
        ],
        PlanSlug::Pro->value => [
            FeatureKey::Products,
            FeatureKey::Orders,
            FeatureKey::Inventory,
            FeatureKey::Notifications,
        ],
    ];

    public function run(): void
    {
        foreach (self::PLAN_FEATURES as $planSlug => $featureKeys) {
            $plan = Plan::where('slug', $planSlug)->first();

            if (! $plan) {
                continue;
            }

            $featureIds = Feature::whereIn('key', array_column($featureKeys, 'value'))
                ->pluck('id')
                ->toArray();

            $plan->features()->syncWithoutDetaching($featureIds);
        }
    }
}
