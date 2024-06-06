<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Cattegory;
use App\Models\User;
class CategoryTest extends TestCase
{
     public function test_api_not_access_unatharize_user_to_categories()
    {
        $response = $this->get('/api/categories');

        $response->assertStatus(401);
    }
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
