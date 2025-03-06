<?php

namespace Tests\Unit;

use App\Exceptions\PermissionDeniedException;
use App\Models\Enterprise;
use App\Models\Role;
use App\Models\User;
use App\Services\PersonnelLicenseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PersonnelLicenseServiceTest extends TestCase
{
    use RefreshDatabase;

    private PersonnelLicenseService $licenseService;
    private Enterprise $enterprise;
    private User $owner;
    private User $adminUser;
    private User $regularAdmin;
    private User $employeeUser;
    private Role $adminRole;
    private Role $employeeRole;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer le service
        $this->licenseService = new PersonnelLicenseService();

        // Créer les rôles avec des niveaux hiérarchiques
        $this->adminRole = Role::create([
            'name' => 'Administrateur',
            'authority' => [
                'personnel' => [
                    'delete' => true,
                ],
            ],
            'hierarchy_level' => 1,
            'color_hex' => '#FF5722',
            'is_shared' => true,
        ]);

        $this->employeeRole = Role::create([
            'name' => 'Employé',
            'authority' => [
                'personnel' => [
                    'delete' => false,
                ],
            ],
            'hierarchy_level' => 10,
            'color_hex' => '#4CAF50',
            'is_shared' => true,
        ]);

        // Créer une entreprise
        $this->enterprise = Enterprise::create([
            'name' => 'Test Enterprise',
            'key' => 'test-key-123',
            'status' => true,
        ]);

        // Créer le propriétaire de l'entreprise
        $this->owner = User::create([
            'firstname' => 'Owner',
            'lastname' => 'Test',
            'email' => 'owner@test.com',
            'password' => bcrypt('password'),
            'enterprise_uuid' => $this->enterprise->uuid,
            'role_uuid' => $this->adminRole->uuid,
        ]);

        // Attribuer le propriétaire à l'entreprise
        $this->enterprise->owner_uuid = $this->owner->uuid;
        $this->enterprise->save();

        // Créer un administrateur (non propriétaire)
        $this->adminUser = User::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'enterprise_uuid' => $this->enterprise->uuid,
            'role_uuid' => $this->adminRole->uuid,
        ]);

        // Créer un autre administrateur pour tester les suppressions entre administrateurs
        $this->regularAdmin = User::create([
            'firstname' => 'Regular',
            'lastname' => 'Admin',
            'email' => 'regular.admin@test.com',
            'password' => bcrypt('password'),
            'enterprise_uuid' => $this->enterprise->uuid,
            'role_uuid' => $this->adminRole->uuid,
        ]);

        // Créer un employé
        $this->employeeUser = User::create([
            'firstname' => 'Employee',
            'lastname' => 'User',
            'email' => 'employee@test.com',
            'password' => bcrypt('password'),
            'enterprise_uuid' => $this->enterprise->uuid,
            'role_uuid' => $this->employeeRole->uuid,
        ]);

        // Simuler le mock pour le logger
        Log::shouldReceive('info')->zeroOrMoreTimes();
        Log::shouldReceive('error')->zeroOrMoreTimes();
    }

    public function test_owner_can_delete_employee_user_license()
    {
        // Le propriétaire tente de supprimer un employé
        $result = $this->licenseService->deleteLicense(
            $this->employeeUser->uuid,
            $this->owner->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que l'utilisateur a été supprimé
        $this->assertDatabaseMissing('users', ['uuid' => $this->employeeUser->uuid]);
        $this->assertEquals($this->employeeUser->uuid, $result['deleted_user']['uuid']);
        $this->assertEquals($this->owner->uuid, $result['deleted_by']);
        $this->assertTrue($result['deleted_by_owner']);
    }

    public function test_owner_can_delete_admin_user_license()
    {
        // Le propriétaire tente de supprimer un administrateur
        $result = $this->licenseService->deleteLicense(
            $this->adminUser->uuid,
            $this->owner->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que l'administrateur a été supprimé
        $this->assertDatabaseMissing('users', ['uuid' => $this->adminUser->uuid]);
        $this->assertEquals($this->adminUser->uuid, $result['deleted_user']['uuid']);
        $this->assertEquals($this->owner->uuid, $result['deleted_by']);
    }

    public function test_admin_can_delete_employee_user_license()
    {
        // Un administrateur (non propriétaire) tente de supprimer un employé
        $result = $this->licenseService->deleteLicense(
            $this->employeeUser->uuid,
            $this->adminUser->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que l'employé a été supprimé
        $this->assertDatabaseMissing('users', ['uuid' => $this->employeeUser->uuid]);
        $this->assertEquals($this->employeeUser->uuid, $result['deleted_user']['uuid']);
        $this->assertEquals($this->adminUser->uuid, $result['deleted_by']);
        $this->assertFalse($result['deleted_by_owner']);
    }

    public function test_admin_cannot_delete_same_level_admin_license()
    {
        // Un administrateur tente de supprimer un autre administrateur de même niveau
        $this->expectException(PermissionDeniedException::class);
        $this->expectExceptionMessage("You can only delete users with a lower hierarchy level than yours");

        $this->licenseService->deleteLicense(
            $this->regularAdmin->uuid,
            $this->adminUser->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que l'administrateur n'a pas été supprimé
        $this->assertDatabaseHas('users', ['uuid' => $this->regularAdmin->uuid]);
    }

    public function test_admin_cannot_delete_owner_license()
    {
        // Un administrateur tente de supprimer le propriétaire
        $this->expectException(PermissionDeniedException::class);

        $this->licenseService->deleteLicense(
            $this->owner->uuid,
            $this->adminUser->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que le propriétaire n'a pas été supprimé
        $this->assertDatabaseHas('users', ['uuid' => $this->owner->uuid]);
    }

    public function test_user_cannot_delete_own_license()
    {
        // Un utilisateur tente de supprimer son propre compte
        $this->expectException(PermissionDeniedException::class);
        $this->expectExceptionMessage("You cannot delete your own account");

        $this->licenseService->deleteLicense(
            $this->adminUser->uuid,
            $this->adminUser->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que l'utilisateur n'a pas été supprimé
        $this->assertDatabaseHas('users', ['uuid' => $this->adminUser->uuid]);
    }

    public function test_owner_cannot_delete_own_license()
    {
        // Le propriétaire tente de supprimer son propre compte
        $this->expectException(PermissionDeniedException::class);
        $this->expectExceptionMessage("You cannot delete your own account");

        $this->licenseService->deleteLicense(
            $this->owner->uuid,
            $this->owner->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que le propriétaire n'a pas été supprimé
        $this->assertDatabaseHas('users', ['uuid' => $this->owner->uuid]);
    }

    public function test_cannot_delete_user_from_different_enterprise()
    {
        // Créer une autre entreprise et un utilisateur
        $anotherEnterprise = Enterprise::create([
            'name' => 'Another Enterprise',
            'key' => 'another-key-123',
            'status' => true,
        ]);

        $anotherUser = User::create([
            'firstname' => 'Another',
            'lastname' => 'User',
            'email' => 'another@test.com',
            'password' => bcrypt('password'),
            'enterprise_uuid' => $anotherEnterprise->uuid,
            'role_uuid' => $this->employeeRole->uuid,
        ]);

        // Tenter de supprimer un utilisateur d'une autre entreprise
        $this->expectException(ModelNotFoundException::class);

        $this->licenseService->deleteLicense(
            $anotherUser->uuid,
            $this->owner->uuid,
            $this->enterprise->uuid
        );

        // Vérifier que l'utilisateur n'a pas été supprimé
        $this->assertDatabaseHas('users', ['uuid' => $anotherUser->uuid]);
    }
}