<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnterpriseRegistrationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_register_enterprise_with_duplicate_name(): void
    {
        Enterprise::factory()->create([
            'name' => 'Test Enterprise'
        ]);

        $response = $this->postJson('/api/enterprise/register', [
            'enterprise_name' => 'Test Enterprise',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'confirm_password' => 'Password123!'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['enterprise_name']);
    }

    public function test_cannot_register_with_duplicate_email(): void
    {
        $enterprise = Enterprise::factory()->create();
        User::factory()->create([
            'email' => 'john@example.com',
            'enterprise_uuid' => $enterprise->uuid
        ]);

        $response = $this->postJson('/api/enterprise/register', [
            'enterprise_name' => 'New Enterprise',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!',
            'confirm_password' => 'Password123!'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_requires_all_fields(): void
    {
        $response = $this->postJson('/api/enterprise/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'enterprise_name',
                'first_name',
                'last_name',
                'email',
                'password'
            ]);
    }
}