<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Models\User;
use App\Services\JwtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AdminLoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_validation_rules()
    {
        $this->postJson('/api/v1/admin/login')->assertInvalid([
            'email', 'password'
        ]);
    }

    public function test_it_should_not_allow_non_admin_user_login()
    {
        // create non admin user
        $user = User::factory()->create([
            'is_admin' => 0
        ]);

        // send request to endpoint and check it returns unauthorized
        $this->postJson('/api/v1/admin/login', [
            'email' => $user->email,
            'password' => 'password'
        ])->assertUnauthorized();
    }

    public function test_it_returns_the_token_on_successful_admin_login(): void
    {
        // create admin user
        $adminUser = User::factory()->create([
            'is_admin' => 1
        ]);

        // mock JwtService to return the 'dummytoken'
        $this->instance(
            JwtService::class,
            Mockery::mock(JwtService::class, function(MockInterface $mock) {
                $mock->shouldReceive('generate')->andReturn('dummytoken');
            })
        );

        // send request to api endpoint
        $result = $this->postJson('/api/v1/admin/login', [
            'email' => $adminUser->email,
            'password' => 'password'
        ])->assertSuccessful();

        // test we are getting correct token
        $this->assertEquals($result['token'], 'dummytoken');
    }
}
