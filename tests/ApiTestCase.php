<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected function bearerHeaders(string $token): array
    {
        return ['Authorization' => 'Bearer '.$token];
    }

    protected function createAuthenticatedToken(): string
    {
        $this->postJson('/auth/register', [
            'email' => 'test@example.com',
            'password' => 'secret123',
        ])->assertCreated();

        $response = $this->post('/auth/login', [
            'username' => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertOk();

        return $response->json('access_token');
    }
}
