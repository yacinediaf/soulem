<?php

use App\Enums\FeatureKey;
use App\Enums\PlanSlug;
use App\Models\Feature;
use App\Models\Plan;
use Database\Seeders\FeatureSeeder;
use Database\Seeders\PlanFeatureSeeder;
use Database\Seeders\PlanSeeder;
use Illuminate\Database\QueryException;

test('features are seeded correctly', function () {
    $this->seed(FeatureSeeder::class);

    $this->assertDatabaseHas('features', ['key' => 'products', 'name' => 'Products']);
    $this->assertDatabaseHas('features', ['key' => 'orders', 'name' => 'Orders']);
    $this->assertDatabaseHas('features', ['key' => 'inventory', 'name' => 'Inventory']);
    $this->assertDatabaseHas('features', ['key' => 'notifications', 'name' => 'Notifications']);
});

test('features are identified by stable keys', function () {
    $this->seed(FeatureSeeder::class);

    foreach (FeatureKey::cases() as $featureKey) {
        $feature = Feature::where('key', $featureKey->value)->first();

        $this->assertNotNull($feature, "Feature with key '{$featureKey->value}' should exist.");
        $this->assertEquals($featureKey->value, $feature->key);
    }
});

test('feature key is unique', function () {
    Feature::create(['name' => 'Products', 'key' => 'products']);

    $this->expectException(QueryException::class);

    Feature::create(['name' => 'Products Duplicate', 'key' => 'products']);
});

test('feature has correct name', function () {
    $this->seed(FeatureSeeder::class);

    $feature = Feature::where('key', FeatureKey::Inventory->value)->first();

    $this->assertEquals('Inventory', $feature->name);
});

test('free plan has products and orders features', function () {
    $this->seed([PlanSeeder::class, FeatureSeeder::class, PlanFeatureSeeder::class]);

    $plan = Plan::where('slug', PlanSlug::Free->value)->first();
    $featureKeys = $plan->features->pluck('key')->toArray();

    $this->assertContains('products', $featureKeys);
    $this->assertContains('orders', $featureKeys);
    $this->assertNotContains('inventory', $featureKeys);
    $this->assertNotContains('notifications', $featureKeys);
});

test('growth plan has all four features', function () {
    $this->seed([PlanSeeder::class, FeatureSeeder::class, PlanFeatureSeeder::class]);

    $plan = Plan::where('slug', PlanSlug::Growth->value)->first();
    $featureKeys = $plan->features->pluck('key')->toArray();

    $this->assertContains('products', $featureKeys);
    $this->assertContains('orders', $featureKeys);
    $this->assertContains('inventory', $featureKeys);
    $this->assertContains('notifications', $featureKeys);
});

test('pro plan has all four features', function () {
    $this->seed([PlanSeeder::class, FeatureSeeder::class, PlanFeatureSeeder::class]);

    $plan = Plan::where('slug', PlanSlug::Pro->value)->first();
    $featureKeys = $plan->features->pluck('key')->toArray();

    $this->assertContains('products', $featureKeys);
    $this->assertContains('orders', $featureKeys);
    $this->assertContains('inventory', $featureKeys);
    $this->assertContains('notifications', $featureKeys);
});

test('plan features relationship works correctly', function () {
    $this->seed([PlanSeeder::class, FeatureSeeder::class, PlanFeatureSeeder::class]);

    $plan = Plan::where('slug', PlanSlug::Free->value)->first();

    $this->assertInstanceOf(Feature::class, $plan->features->first());
    $this->assertEquals(2, $plan->features->count());
});

test('feature belongs to multiple plans', function () {
    $this->seed([PlanSeeder::class, FeatureSeeder::class, PlanFeatureSeeder::class]);

    $feature = Feature::where('key', FeatureKey::Products->value)->first();

    $this->assertEquals(3, $feature->plans->count());
});

test('plan feature pivot is unique', function () {
    $this->seed([PlanSeeder::class, FeatureSeeder::class, PlanFeatureSeeder::class]);

    $plan = Plan::where('slug', PlanSlug::Free->value)->first();
    $feature = Feature::where('key', FeatureKey::Products->value)->first();

    $this->expectException(QueryException::class);

    $plan->features()->attach($feature->id);
});
