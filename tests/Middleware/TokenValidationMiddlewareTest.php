<?php

namespace Tests\Middleware;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenValidationMiddlewareTest extends TestCase
{
    public function test_token_validation_middleware_success(): void
    {
        $user = $this->login();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(
            '/api/verify-jwt',
            [],
            ['Authorization' => 'Bearer ' . $token]
        );
        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($user) {
                $json
                    ->hasAll(['data', 'status', 'message', 'data.email']);
            });
    }

    public function test_token_validation_middleware_wrong_token(): void
    {
        $user = $this->login();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson(
            '/api/verify-jwt',
            [],
            ['Authorization' => 'Bearer ' . $token . 'wrong']
        );
        $response
            ->assertStatus(401)
            ->assertJson(function (AssertableJson $json) use ($user) {
                $json
                    ->hasAll(['source', 'status', 'message', 'error', 'error.type', 'error.details'])
                    ->where('message', 'Token is invalid: Token Signature could not be verified.');
            });
    }
}
