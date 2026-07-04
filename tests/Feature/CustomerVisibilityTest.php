<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomerVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_sees_all_customers(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->syncRoles(['Admin']);

        $ownerA = User::factory()->staff()->create();
        $ownerA->syncRoles(['Sales']);

        $ownerB = User::factory()->staff()->create();
        $ownerB->syncRoles(['Sales']);

        $customerA = $this->createCustomer($ownerA, 'ADM-A');
        $customerB = $this->createCustomer($ownerB, 'ADM-B');

        Sanctum::actingAs($admin);

        $response = $this->getJson('/api/customers')
            ->assertOk();

        $ids = collect($response->json('data.data'))->pluck('id')->all();

        $this->assertContains($customerA->id, $ids);
        $this->assertContains($customerB->id, $ids);
    }

    public function test_manager_sees_all_customers(): void
    {
        $manager = User::factory()->staff()->create();
        $manager->syncRoles(['Manager']);

        $ownerA = User::factory()->staff()->create();
        $ownerA->syncRoles(['Sales']);

        $ownerB = User::factory()->staff()->create();
        $ownerB->syncRoles(['Sales']);

        $customerA = $this->createCustomer($ownerA, 'MGR-A');
        $customerB = $this->createCustomer($ownerB, 'MGR-B');

        Sanctum::actingAs($manager);

        $response = $this->getJson('/api/customers')
            ->assertOk();

        $ids = collect($response->json('data.data'))->pluck('id')->all();

        $this->assertContains($customerA->id, $ids);
        $this->assertContains($customerB->id, $ids);
    }

    public function test_sales_user_only_sees_own_customers_and_gets_forbidden_for_others(): void
    {
        $salesA = User::factory()->staff()->create();
        $salesA->syncRoles(['Sales']);

        $salesB = User::factory()->staff()->create();
        $salesB->syncRoles(['Sales']);

        $ownCustomer = $this->createCustomer($salesA, 'SAL-OWN');
        $otherCustomer = $this->createCustomer($salesB, 'SAL-OTH');

        Sanctum::actingAs($salesA);

        $response = $this->getJson('/api/customers')
            ->assertOk();

        $ids = collect($response->json('data.data'))->pluck('id')->all();

        $this->assertContains($ownCustomer->id, $ids);
        $this->assertNotContains($otherCustomer->id, $ids);

        $this->getJson("/api/customers/{$otherCustomer->id}")
            ->assertForbidden()
            ->assertJson([
                'message' => 'You do not have access to this customer.',
            ]);

        $this->putJson("/api/customers/{$otherCustomer->id}", [
            'code' => $otherCustomer->code,
            'name' => 'Attempted Update',
            'phone' => $otherCustomer->phone,
            'email' => $otherCustomer->email,
            'address' => $otherCustomer->address,
            'area' => $otherCustomer->area,
            'credit_limit' => $otherCustomer->credit_limit,
            'status' => $otherCustomer->status,
            'remarks' => $otherCustomer->remarks,
        ])
            ->assertForbidden()
            ->assertJson([
                'message' => 'You do not have access to this customer.',
            ]);
    }

    protected function createCustomer(User $owner, string $code): Customer
    {
        return Customer::query()->create([
            'code' => $code,
            'name' => "Customer {$code}",
            'phone' => '0123456789',
            'email' => strtolower($code).'@example.com',
            'address' => 'Test address',
            'area' => 'KL',
            'credit_limit' => 1000,
            'status' => 'active',
            'remarks' => 'Seeded for visibility test',
            'created_by' => $owner->id,
        ]);
    }
}
