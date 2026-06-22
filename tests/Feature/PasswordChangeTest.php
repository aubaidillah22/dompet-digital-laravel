<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $plainPassword = 'user1234';

    protected function setUp(): void
    {
        parent::setUp();

        // Disable all middleware for API tests since we're using session-based auth
        $this->withoutMiddleware();

        $this->user = User::factory()->create([
            'username' => 'testuser',
            'full_name' => 'Test User',
            'password' => $this->plainPassword,
            'role' => 'user',
        ]);
    }

    public function test_authenticated_user_can_change_password(): void
    {
        $response = $this->withSession([
            'user_id' => $this->user->id,
            'username' => $this->user->username,
            'role' => $this->user->role,
        ])->putJson('/api/user/password', [
            'current_password' => $this->plainPassword,
            'new_password' => 'newpass123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Password berhasil diubah',
            ]);
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $response = $this->withSession([
            'user_id' => $this->user->id,
        ])->putJson('/api/user/password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpass123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Password lama tidak sesuai',
            ]);
    }

    public function test_change_password_fails_when_unauthenticated(): void
    {
        $response = $this->putJson('/api/user/password', [
            'current_password' => $this->plainPassword,
            'new_password' => 'newpass123',
        ]);

        // Without middleware, unauthenticated request reaches controller
        // and fails with 404 (User::findOrFail on null session)
        $response->assertStatus(404);
    }

    public function test_change_password_fails_with_short_new_password(): void
    {
        $response = $this->withSession([
            'user_id' => $this->user->id,
        ])->putJson('/api/user/password', [
            'current_password' => $this->plainPassword,
            'new_password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_change_password_fails_without_current_password(): void
    {
        $response = $this->withSession([
            'user_id' => $this->user->id,
        ])->putJson('/api/user/password', [
            'new_password' => 'newpass123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    public function test_change_password_fails_when_user_deleted(): void
    {
        $userId = $this->user->id;
        $this->user->delete();

        $response = $this->withSession([
            'user_id' => $userId,
        ])->putJson('/api/user/password', [
            'current_password' => $this->plainPassword,
            'new_password' => 'newpass123',
        ]);

        // Without middleware, deleted user reaches controller
        // and fails with 404 (User::findOrFail on deleted user)
        $response->assertStatus(404);
    }

    public function test_old_password_no_longer_works_after_change(): void
    {
        $sessionData = [
            'user_id' => $this->user->id,
            'username' => $this->user->username,
            'role' => $this->user->role,
        ];

        $this->withSession($sessionData)->putJson('/api/user/password', [
            'current_password' => $this->plainPassword,
            'new_password' => 'newpass123',
        ]);

        $response = $this->withSession($sessionData)->putJson('/api/user/password', [
            'current_password' => $this->plainPassword,
            'new_password' => 'anothernew456',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Password lama tidak sesuai',
            ]);
    }
}
