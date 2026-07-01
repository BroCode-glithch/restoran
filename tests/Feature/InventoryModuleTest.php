<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_manager_can_view_inventory_page(): void
    {
        $user = User::factory()->create([
            'role' => 'manager',
            'is_active' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get(route('admin.inventory.index'));

        $response->assertOk();
        $response->assertSee('Manage inventory with confidence');
    }
}
