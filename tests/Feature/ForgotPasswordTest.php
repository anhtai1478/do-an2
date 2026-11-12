<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_forgot_password_page(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Quên mật khẩu');
        $response->assertSee('name="email"', false);
    }

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'testuser@example.com',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'testuser@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_user_cannot_request_reset_with_nonexistent_email(): void
    {
        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => 'notfound@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_view_reset_password_page_with_token(): void
    {
        $user = User::factory()->create([
            'email' => 'testreset@example.com',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', [
            'token' => $token,
            'email' => 'testreset@example.com',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Đặt lại mật khẩu mới');
        $response->assertSee('name="password"', false);
        $response->assertSee('name="password_confirmation"', false);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'resetme@example.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'resetme@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('login'));
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }
}

