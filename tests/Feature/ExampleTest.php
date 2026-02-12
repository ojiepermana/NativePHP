<?php

test('root redirects to login for guests', function () {
    $response = $this->get('/');

    $response->assertRedirect('/login');
});

test('dashboard page loads for authenticated users', function () {
    $user = \App\Models\User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
    $response->assertSee('Dashboard');
});
