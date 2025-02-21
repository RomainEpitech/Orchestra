<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Enterprise;
use App\Services\KeyGeneratorService;
use App\Services\EnterpriseRegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\EnterpriseCreatedMail;

class EnterpriseRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private EnterpriseRegistrationService $registrationService;
    private $keyGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer le mock avec PHPUnit
        $this->keyGenerator = $this->getMockBuilder(KeyGeneratorService::class)
            ->disableOriginalConstructor()
            ->getMock();
        
        $this->registrationService = new EnterpriseRegistrationService($this->keyGenerator);
        
        Mail::fake();
    }

    public function test_can_create_enterprise_with_owner(): void
    {
        // Configurer le mock
        $this->keyGenerator
            ->expects($this->once())
            ->method('generateUniqueKey')
            ->willReturn([
                'readable' => 'TestKey123',
                'hashed' => Hash::make('TestKey123')
            ]);

        $data = [
            'enterprise_name' => 'Test Enterprise',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'Password123!'
        ];

        // Act
        $result = $this->registrationService->register($data);

        // Assert
        $this->assertDatabaseHas('enterprises', [
            'name' => 'Test Enterprise',
            'status' => true
        ]);

        $this->assertDatabaseHas('users', [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john@example.com'
        ]);

        $enterprise = Enterprise::first();
        $user = User::first();

        // Assertions des relations
        $this->assertNotNull($enterprise);
        $this->assertNotNull($user);
        $this->assertEquals($enterprise->owner_uuid, $user->uuid);
        $this->assertEquals($user->enterprise_uuid, $enterprise->uuid);

        // Vérification de l'envoi d'email
        Mail::assertSent(EnterpriseCreatedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });

        // Vérification de la structure du résultat
        $this->assertArrayHasKey('enterprise', $result);
        $this->assertArrayHasKey('owner', $result);
        $this->assertEquals('Test Enterprise', $result['enterprise']['name']);
        $this->assertEquals('john@example.com', $result['owner']['email']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}