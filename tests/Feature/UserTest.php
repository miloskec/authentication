<?php

namespace Tests\Feature;

use App\Notifications\UserCreatedKafkaNotification;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_registration_with_empty_request_data_error_checking_structure_and_validation_with_messages_from_response(): void
    {
        $response = $this->postJson('/api/register');
        $response
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->hasAll(['status', 'source', 'message', 'error', 'error.type', 'error.details'])
                    ->where('error.details.username', ['The username field is required.'])
                    ->where('error.details.full_name', ['The full name field is required.'])
                    ->where('error.details.email', ['The email field is required.'])
                    ->where('error.details.password', ['The password field is required.']);
            });
    }

    public function test_admin_registration_success(): void
    {
        $response = $this->postJson(
            '/api/register',
            [
                'username' => 'adminuser',
                'full_name' => 'Test User',
                'email' => fake()->unique()->safeEmail,
                'password' => $this->password,
                'password_confirmation' => $this->password,
                'is_admin' => true,
            ]
        );

        $response
            ->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->hasAll(['data', 'status', 'message', 'data.id', 'data.email', 'data.username', 'data.full_name', 'data.created_at', 'data.updated_at'])
                    ->where('status', 'success')
                    ->where('message', 'User data retrieved successfully.');
            });
    }

    public function test_user_login_with_empty_request_data_error_checking_structure_and_validation_messages_of_response(): void
    {
        $response = $this->postJson('/api/login');
        $response
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json
                    ->hasAll(['status', 'source', 'message', 'error', 'error.type', 'error.details'])
                    ->where('error.details.email', ['The email field is required.'])
                    ->where('error.details.password', ['The password field is required.']);
            });
    }

    public function test_user_login_success(): void
    {
        $user = $this->login();

        $response = $this->postJson(
            '/api/login',
            [
                'email' => $user->email,
                'password' => $this->password,
            ]
        );

        $this->assertAuthenticated();
        $response
            ->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($user) {
                $json
                    ->hasAll(['data', 'status', 'message', 'data.user', 'data.token.access_token']);
            });
    }

    public function test_user_login_wrong_password(): void
    {
        $user = $this->login();

        $response = $this->postJson(
            '/api/login',
            [
                'email' => $user->email,
                'password' => $this->password . 'wrong',
            ]
        );

        $this->assertGuest();
        $response
            ->assertStatus(401)
            ->assertJson(function (AssertableJson $json) use ($user) {
                $json
                    ->hasAll(['source', 'status', 'message', 'error', 'error.type', 'error.details']);
            });
    }
}
