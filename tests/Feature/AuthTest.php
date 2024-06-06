<?php

namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;


class AuthTest extends TestCase
{
public function test_api_register_user()
    {
        $response = $this->postJson('api/register',
            [
                'name'  =>  $name = 'Test',
                'email'  =>  $email = rand(1,2) .'testuser@example.com',
                'password'  =>  $password = 'password',
                'password_confirmation'  =>  $password_confirmation = 'password',

            ],
        );
        $response->assertStatus(200);
        // ->assertJsonStructure(['access_token']);
    }

    public function test_api_login_user()
    {
        $user = User::factory()->create();

        $response = $this->postJson('api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

  public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
