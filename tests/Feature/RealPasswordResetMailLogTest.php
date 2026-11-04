<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class RealPasswordResetMailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_real_password_reset_writes_to_log(): void
    {
        config(['mail.default' => 'log']);

        $user = User::factory()->create([
            'email' => 'logtest@aptimedha.com',
        ]);

        $logPath = storage_path('logs/laravel.log');
        $initialSize = File::exists($logPath) ? File::size($logPath) : 0;

        $response = $this->post('/forgot-password', [
            'email' => 'logtest@aptimedha.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertFileExists($logPath);
        $newContent = file_get_contents($logPath, false, null, $initialSize);
        $this->assertStringContainsString('Reset Password', $newContent);
        $this->assertStringContainsString('reset-password', $newContent);
        $this->assertStringContainsString('email=logtest%40aptimedha.com', $newContent);
    }
}
