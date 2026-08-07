<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('google redirect route is accessible', function () {
    Socialite::fake('google');

    $response = $this->get(route('socialite.google.redirect'));

    $response->assertRedirect();
});

test('google callback creates new user', function () {
    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-123',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]));

    $response = $this->get(route('socialite.google.callback'));

    $response->assertRedirect(route('stores.create'));

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'google_id' => 'google-123',
    ]);

    $this->assertAuthenticated();
});

test('google callback links google account to existing user by email', function () {
    $existingUser = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-123',
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]));

    $response = $this->get(route('socialite.google.callback'));

    $response->assertRedirect(route('stores.create'));

    $this->assertDatabaseHas('users', [
        'id' => $existingUser->id,
        'google_id' => 'google-123',
    ]);

    $this->assertAuthenticated();
});

test('google callback does not create duplicate user when email exists', function () {
    $existingUser = User::factory()->create([
        'email' => 'john@example.com',
    ]);

    Socialite::fake('google', SocialiteUser::fake([
        'id' => 'google-456',
        'name' => 'John Updated',
        'email' => 'john@example.com',
    ]));

    $response = $this->get(route('socialite.google.callback'));

    $response->assertRedirect(route('stores.create'));

    $this->assertDatabaseCount('users', 1);

    $this->assertDatabaseHas('users', [
        'id' => $existingUser->id,
        'google_id' => 'google-456',
    ]);

    $this->assertAuthenticated();
});
