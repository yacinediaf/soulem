<?php

use App\Enums\FeatureKey;
use App\Models\Feature;
use Database\Seeders\FeatureSeeder;
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
