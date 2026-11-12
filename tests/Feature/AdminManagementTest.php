<?php

namespace Tests\Feature;

use App\Models\History;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'level' => 2,
        ]);

        $this->member = User::factory()->create([
            'name' => 'John Member',
            'email' => 'member@test.com',
            'level' => 0,
        ]);
    }

    public function test_non_admin_cannot_access_admin_user_list(): void
    {
        $response = $this->actingAs($this->member)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_user_list_with_levels(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Admin User');
        $response->assertSee('John Member');
        $response->assertSee('Admin');
        $response->assertSee('Member');
    }

    public function test_admin_can_edit_and_update_member_level(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $this->member->id));
        $response->assertStatus(200);
        $response->assertSee($this->member->name);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.users.update', $this->member->id), [
            'name' => 'Updated Member Name',
            'email' => 'member@test.com',
            'level' => 1,
            'phone' => '0987654321',
        ]);

        $updateResponse->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->member->id,
            'name' => 'Updated Member Name',
            'level' => 1,
            'phone' => '0987654321',
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->admin->id));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_delete_member(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.users.destroy', $this->member->id));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $this->member->id]);
    }

    public function test_admin_can_view_product_list_and_search_by_member_name(): void
    {
        $otherMember = User::factory()->create([
            'name' => 'Alice Seller',
            'email' => 'alice@test.com',
            'level' => 0,
        ]);

        $product1 = Product::create([
            'user_id' => $this->member->id,
            'name' => 'John Shoes',
            'price' => 50,
            'category' => 'Shoes',
            'brand' => 'Nike',
            'is_on_sale' => false,
        ]);

        $product2 = Product::create([
            'user_id' => $otherMember->id,
            'name' => 'Alice Dress',
            'price' => 80,
            'category' => 'Dress',
            'brand' => 'Zara',
            'is_on_sale' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSee('John Shoes');
        $response->assertSee('Alice Dress');

        // Test search by member name
        $searchResponse = $this->actingAs($this->admin)->get(route('admin.products.index', ['member_name' => 'Alice']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Alice Dress');
        $searchResponse->assertDontSee('John Shoes');
    }

    public function test_admin_can_edit_and_update_product(): void
    {
        $product = Product::create([
            'user_id' => $this->member->id,
            'name' => 'Invalid Product Name',
            'price' => 20,
            'category' => 'Test',
            'brand' => 'BrandTest',
            'is_on_sale' => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product->id));
        $response->assertStatus(200);

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.products.update', $product->id), [
            'name' => 'Corrected Product Name',
            'price' => 35,
            'category' => 'Fashion',
            'brand' => 'Nike',
            'is_on_sale' => '1',
            'discount_percent' => 10,
        ]);

        $updateResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Corrected Product Name',
            'price' => 35,
            'is_on_sale' => true,
            'discount_percent' => 10,
        ]);
    }

    public function test_admin_can_delete_invalid_product(): void
    {
        $product = Product::create([
            'user_id' => $this->member->id,
            'name' => 'Fake Product',
            'price' => 10,
            'category' => 'Spam',
            'brand' => 'SpamBrand',
            'is_on_sale' => false,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product->id));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_view_and_delete_order_histories(): void
    {
        $history = History::create([
            'name' => 'Customer Bob',
            'email' => 'bob@buyer.com',
            'phone' => '0123456789',
            'id_user' => $this->member->id,
            'price' => 150.50,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.histories.index'));
        $response->assertStatus(200);
        $response->assertSee('Customer Bob');
        $response->assertSee('bob@buyer.com');
        $response->assertSee('$150.50');

        $deleteResponse = $this->actingAs($this->admin)->delete(route('admin.histories.destroy', $history->id));
        $deleteResponse->assertRedirect(route('admin.histories.index'));
        $this->assertDatabaseMissing('histories', ['id' => $history->id]);
    }
}
