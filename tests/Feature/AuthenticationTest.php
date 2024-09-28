<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function user_can_login_successfully()
    {
        $user = User::factory()->create([
            'email' => 'testlogin@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $response = $this->post('/api/login', [
            'email' => 'testlogin@gmail.com',
            'password' => '12345678'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'testWronglogin@gmail.com',
            'password' => bcrypt('12345678')
        ]);

        $response = $this->post('/api/login', [
            'email' => 'testWronglogin@gmail.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
