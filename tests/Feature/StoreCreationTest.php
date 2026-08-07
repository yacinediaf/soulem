<?php

use App\Enums\PlanSlug;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;

test('guests are redirected to the login page when accessing store creation', function () {
    $response = $this->get(route('stores.create'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can view the store creation page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('stores.create'));
    $response->assertOk();
});

test('authenticated users can create a store', function () {
    Plan::updateOrCreate(['slug' => PlanSlug::Free->value], ['name' => 'Free']);
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'My Store',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('stores', [
        'name' => 'My Store',
        'slug' => 'my-store',
        'user_id' => $user->id,
    ]);
});

test('store receives the free plan', function () {
    $plan = Plan::updateOrCreate(['slug' => PlanSlug::Free->value], ['name' => 'Free']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'My Store',
    ]);

    $store = $user->stores()->first();
    $this->assertEquals($plan->id, $store->plan_id);
});

test('store becomes the current store for the user', function () {
    Plan::updateOrCreate(['slug' => PlanSlug::Free->value], ['name' => 'Free']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'My Store',
    ]);

    $user->refresh();
    $this->assertNotNull($user->current_store_id);
    $this->assertEquals($user->stores()->first()->id, $user->current_store_id);
});

test('store name must be unique', function () {
    Plan::updateOrCreate(['slug' => PlanSlug::Free->value], ['name' => 'Free']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'My Store',
    ]);

    $user2 = User::factory()->create();

    $response = $this->actingAs($user2)->post(route('stores.store'), [
        'name' => 'My Store',
    ]);

    $response->assertSessionHasErrors('name');
});

test('store name is required', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('stores.store'), []);

    $response->assertSessionHasErrors('name');
});

test('store slug is generated from name', function () {
    Plan::updateOrCreate(['slug' => PlanSlug::Free->value], ['name' => 'Free']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'My Awesome Store!',
    ]);

    $this->assertDatabaseHas('stores', [
        'slug' => 'my-awesome-store',
    ]);
});

test('store name check endpoint returns available when name is not taken', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->getJson(route('api.store.check', ['name' => 'Unique Store']));

    $response->assertJson(['available' => true]);
});

test('store name check endpoint returns unavailable when name is taken', function () {
    Plan::updateOrCreate(['slug' => PlanSlug::Free->value], ['name' => 'Free']);
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('stores.store'), [
        'name' => 'Taken Store',
    ]);

    $response = $this->actingAs($user)->getJson(route('api.store.check', ['name' => 'Taken Store']));

    $response->assertJson(['available' => false]);
});

test('plans are seeded correctly', function () {
    $this->seed(PlanSeeder::class);

    $this->assertDatabaseHas('plans', ['slug' => 'free', 'name' => 'Free']);
    $this->assertDatabaseHas('plans', ['slug' => 'growth', 'name' => 'Growth']);
    $this->assertDatabaseHas('plans', ['slug' => 'pro', 'name' => 'Pro']);
});
