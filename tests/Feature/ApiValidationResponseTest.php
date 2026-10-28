<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiValidationResponseTest extends TestCase
{
    public function test_api_validation_errors_return_422(): void
    {
        $this->postJson('/api/login', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['username', 'password']);
    }
}
