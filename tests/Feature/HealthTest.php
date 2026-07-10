<?php

namespace Tests\Feature;

use Tests\ApiTestCase;

class HealthTest extends ApiTestCase
{
    public function test_root(): void
    {
        $this->getJson('/')
            ->assertOk()
            ->assertExactJson(['message' => 'Hello from laravel-101']);
    }

    public function test_health(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertExactJson(['status' => 'ok']);
    }
}
