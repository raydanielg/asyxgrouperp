<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RbacAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function makeRole(string $name, array $permNames): Role
    {
        $role = Role::create(['name' => $name, 'label' => ucfirst($name), 'editable' => true]);
        $ids = [];
        foreach ($permNames as $pn) {
            $ids[] = Permission::firstOrCreate(['name' => $pn], ['label' => $pn, 'module' => explode('-', $pn)[0]])->id;
        }
        $role->permissions()->sync($ids);
        return $role;
    }

    private function makeUser(array $attrs = []): User
    {
        $company = Company::firstOrCreate(
            ['name' => 'Test Co'],
            ['short_code' => 'TST', 'is_active' => true]
        );
        return User::create(array_merge([
            'name' => 'Test User',
            'email' => 'u' . uniqid() . '@test.com',
            'password' => Hash::make('password'),
            'role' => 'user',
            'company_id' => $company->id,
            'is_enable_login' => true,
        ], $attrs));
    }

    /** @test */
    public function role_permissions_resolve_from_database(): void
    {
        $role = $this->makeRole('test_accountant', ['view-invoices', 'create-invoices']);
        $user = $this->makeUser(['role' => 'test_accountant']);
        $user->roles()->sync([$role->id]);

        Cache::forget("user_perms_{$user->id}");
        $this->assertTrue($user->hasPermission('view-invoices'));
        $this->assertTrue($user->hasPermission('create-invoices'));
        $this->assertFalse($user->hasPermission('delete-invoices'));
    }

    /** @test */
    public function role_permission_changes_take_effect_after_cache_clear(): void
    {
        $role = $this->makeRole('test_role', ['perm-a']);
        $user = $this->makeUser();
        $user->roles()->sync([$role->id]);
        Cache::forget("user_perms_{$user->id}");

        $this->assertTrue($user->hasPermission('perm-a'));

        // Change DB permissions and invalidate
        $permB = Permission::firstOrCreate(['name' => 'perm-b'], ['label' => 'perm-b']);
        $role->permissions()->attach($permB->id);
        Cache::forget("user_perms_{$user->id}");

        $this->assertTrue($user->hasPermission('perm-b'));
    }

    /** @test */
    public function direct_grant_adds_permission(): void
    {
        $role = $this->makeRole('test_gr', ['perm-x']);
        $user = $this->makeUser();
        $user->roles()->sync([$role->id]);

        $perm = Permission::firstOrCreate(['name' => 'special-perm'], ['label' => 'special']);
        $user->directPermissions()->create(['permission_id' => $perm->id, 'granted' => true]);
        Cache::forget("user_perms_{$user->id}");

        $this->assertTrue($user->hasPermission('special-perm'));
        $this->assertContains('special-perm', $user->resolvePermissionNames()['granted']);
    }

    /** @test */
    public function explicit_denial_overrides_role_permission(): void
    {
        $role = $this->makeRole('test_dn', ['payroll-approve']);
        $user = $this->makeUser();
        $user->roles()->sync([$role->id]);
        Cache::forget("user_perms_{$user->id}");
        $this->assertTrue($user->hasPermission('payroll-approve'));

        $perm = Permission::where('name', 'payroll-approve')->first();
        $user->directPermissions()->create(['permission_id' => $perm->id, 'granted' => false]);
        Cache::forget("user_perms_{$user->id}");

        $this->assertFalse($user->hasPermission('payroll-approve'));
        $this->assertContains('payroll-approve', $user->resolvePermissionNames()['denied']);
        $this->assertNotContains('payroll-approve', $user->resolvePermissionNames()['effective']);
    }

    /** @test */
    public function revoking_override_restores_role_permissions(): void
    {
        $role = $this->makeRole('test_rv', ['perm-keep']);
        $user = $this->makeUser();
        $user->roles()->sync([$role->id]);

        $perm = Permission::where('name', 'perm-keep')->first();
        $o = $user->directPermissions()->create(['permission_id' => $perm->id, 'granted' => false]);
        Cache::forget("user_perms_{$user->id}");
        $this->assertFalse($user->hasPermission('perm-keep'));

        $o->delete();
        Cache::forget("user_perms_{$user->id}");
        $this->assertTrue($user->hasPermission('perm-keep'));
    }

    /** @test */
    public function non_superadmin_cannot_manage_direct_permissions(): void
    {
        $admin = $this->makeUser(['role' => 'admin']);
        $target = $this->makeUser();
        $perm = Permission::firstOrCreate(['name' => 'any-perm'], ['label' => 'any']);

        $response = $this->actingAs($admin)->post("/admin/users/{$target->id}/permissions", [
            'permission' => 'any-perm',
            'action' => 'grant',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('user_permissions', ['user_id' => $target->id]);
    }

    /** @test */
    public function superadmin_can_grant_deny_and_revoke(): void
    {
        $super = $this->makeUser(['role' => 'superadmin']);
        $target = $this->makeUser();
        Permission::firstOrCreate(['name' => 'p-1'], ['label' => 'p-1']);

        $this->actingAs($super)->post("/admin/users/{$target->id}/permissions", [
            'permission' => 'p-1', 'action' => 'grant', 'reason' => 'test',
        ])->assertRedirect();
        $this->assertDatabaseHas('user_permissions', ['user_id' => $target->id, 'granted' => 1]);

        $this->actingAs($super)->post("/admin/users/{$target->id}/permissions", [
            'permission' => 'p-1', 'action' => 'deny',
        ]);
        $this->assertDatabaseHas('user_permissions', ['user_id' => $target->id, 'granted' => 0]);

        $this->actingAs($super)->post("/admin/users/{$target->id}/permissions", [
            'permission' => 'p-1', 'action' => 'revoke',
        ]);
        $this->assertDatabaseMissing('user_permissions', ['user_id' => $target->id]);
    }

    /** @test */
    public function non_superadmin_cannot_create_superadmin_user(): void
    {
        $admin = $this->makeUser(['role' => 'admin']);
        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Evil',
            'email' => 'evil@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'superadmin',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'evil@test.com']);
    }

    /** @test */
    public function last_superadmin_cannot_be_demoted(): void
    {
        $super = $this->makeUser(['role' => 'superadmin']);
        $response = $this->actingAs($super)->patch("/admin/users/{$super->id}", [
            'name' => 'Super', 'email' => $super->email, 'role' => 'user',
        ]);
        $response->assertStatus(403);
        $this->assertDatabaseHas('users', ['id' => $super->id, 'role' => 'superadmin']);
    }

    /** @test */
    public function admin_role_bypasses_permission_catalogue(): void
    {
        // Documented behavior: system role 'admin' bypasses the catalogue
        // for backoffice access — business scoping is applied separately.
        $admin = $this->makeUser(['role' => 'admin']);
        $this->assertTrue($admin->hasPermission('any-random-permission'));
    }
}
