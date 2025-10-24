<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('book appointment page auto-populates user information', function () {
    // Create a user with phone and address
    $user = User::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'address' => '123 Main Street, City',
        'barangay' => 'Barangay 1',
        'password' => bcrypt('password'),
        'role' => 'user'
    ]);

    // Login as the user
    $this->actingAs($user);

    // Visit the book appointment page
    $response = $this->get('/patient/book-appointment');

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert that the user's information is present in the form
    $response->assertSee('value="John Doe"', false);
    $response->assertSee('value="1234567890"', false);
    $response->assertSee('123 Main Street, City', false);
});

test('book appointment page works for users without phone and address', function () {
    // Create a user without phone and address (common scenario)
    $user = User::create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'barangay' => 'Barangay 2',
        'password' => bcrypt('password'),
        'role' => 'user'
    ]);

    // Login as the user
    $this->actingAs($user);

    // Visit the book appointment page
    $response = $this->get('/patient/book-appointment');

    // Assert the page loads successfully
    $response->assertStatus(200);

    // Assert that the user's name is present but phone and address are empty
    $response->assertSee('value="Jane Doe"', false);
    $response->assertSee('value=""', false); // Empty phone field
    $response->assertSee('Your information has been pre-filled from your account', false);
});
