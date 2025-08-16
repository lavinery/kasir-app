<?php
// tests/Feature/FavoriteProductTest.php

namespace Tests\Feature;

use App\Models\FavoriteProduct;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteProductTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create(['level' => 1]);

        // Create regular user
        $this->user = User::factory()->create(['level' => 2]);

        // Create product
        $this->product = Produk::factory()->create([
            'nama_produk' => 'Test Product',
            'kode_produk' => 'TEST001',
            'harga_jual' => 10000,
        ]);
    }

    /** @test */
    public function admin_can_access_favorites_settings()
    {
        $response = $this->actingAs($this->admin)
            ->get('/settings/favorites');

        $response->assertStatus(200);
        $response->assertViewIs('settings.favorites');
    }

    /** @test */
    public function non_admin_cannot_access_favorites_settings()
    {
        $response = $this->actingAs($this->user)
            ->get('/settings/favorites');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_add_product_to_favorites()
    {
        $response = $this->actingAs($this->admin)
            ->post('/settings/favorites/add', [
                'product_id' => $this->product->id_produk
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('favorite_products', [
            'product_id' => $this->product->id_produk,
            'is_active' => true,
            'sort_order' => 1
        ]);
    }

    /** @test */
    public function cannot_add_same_product_twice_to_favorites()
    {
        // Add product first time
        FavoriteProduct::create([
            'product_id' => $this->product->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        // Try to add same product again
        $response = $this->actingAs($this->admin)
            ->post('/settings/favorites/add', [
                'product_id' => $this->product->id_produk
            ]);

        $response->assertSessionHasErrors(['product_id']);
    }

    /** @test */
    public function admin_can_toggle_favorite_status()
    {
        $favorite = FavoriteProduct::create([
            'product_id' => $this->product->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->patch("/settings/favorites/toggle/{$favorite->id}");

        $response->assertJson(['success' => true, 'is_active' => false]);

        $favorite->refresh();
        $this->assertFalse($favorite->is_active);
    }

    /** @test */
    public function admin_can_reorder_favorites()
    {
        $favorite1 = FavoriteProduct::create([
            'product_id' => $this->product->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        $product2 = Produk::factory()->create();
        $favorite2 = FavoriteProduct::create([
            'product_id' => $product2->id_produk,
            'sort_order' => 2,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->patch('/settings/favorites/reorder', [
                'items' => [
                    ['id' => $favorite1->id, 'sort_order' => 2],
                    ['id' => $favorite2->id, 'sort_order' => 1],
                ]
            ]);

        $response->assertJson(['success' => true]);

        $favorite1->refresh();
        $favorite2->refresh();

        $this->assertEquals(2, $favorite1->sort_order);
        $this->assertEquals(1, $favorite2->sort_order);
    }

    /** @test */
    public function admin_can_delete_favorite()
    {
        $favorite = FavoriteProduct::create([
            'product_id' => $this->product->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->delete("/settings/favorites/{$favorite->id}");

        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('favorite_products', ['id' => $favorite->id]);
    }

    /** @test */
    public function transaction_favorites_endpoint_returns_maximum_10_active_favorites()
    {
        // Create 15 active favorites
        for ($i = 1; $i <= 15; $i++) {
            $product = Produk::factory()->create([
                'nama_produk' => "Product {$i}",
                'kode_produk' => "PROD{$i}",
                'harga_jual' => 10000 + ($i * 1000)
            ]);

            FavoriteProduct::create([
                'product_id' => $product->id_produk,
                'sort_order' => $i,
                'is_active' => true
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get('/transactions/favorites');

        $response->assertStatus(200);
        $data = $response->json();

        // Should return only 10 items even though we have 15
        $this->assertCount(10, $data);

        // Should be ordered by sort_order
        $this->assertEquals('Product 1', $data[0]['nama']);
        $this->assertEquals('Product 10', $data[9]['nama']);
    }

    /** @test */
    public function transaction_favorites_only_returns_active_favorites()
    {
        // Create 5 active and 5 inactive favorites
        for ($i = 1; $i <= 10; $i++) {
            $product = Produk::factory()->create([
                'nama_produk' => "Product {$i}",
                'kode_produk' => "PROD{$i}",
                'harga_jual' => 10000
            ]);

            FavoriteProduct::create([
                'product_id' => $product->id_produk,
                'sort_order' => $i,
                'is_active' => $i <= 5 // First 5 are active
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get('/transactions/favorites');

        $response->assertStatus(200);
        $data = $response->json();

        // Should return only 5 active items
        $this->assertCount(5, $data);

        // All returned items should be from active favorites
        foreach ($data as $item) {
            $this->assertStringContainsString('Product', $item['nama']);
            $this->assertLessThanOrEqual(5, (int)str_replace(['Product ', 'PROD'], '', $item['kode']));
        }
    }

    /** @test */
    public function search_products_api_excludes_already_favorited_products()
    {
        // Create favorite
        FavoriteProduct::create([
            'product_id' => $this->product->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        // Create another product
        $product2 = Produk::factory()->create([
            'nama_produk' => 'Another Test Product',
            'kode_produk' => 'TEST002'
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/api/products/search?q=Test');

        $response->assertStatus(200);
        $data = $response->json();

        // Should only return product2, not the favorited product
        $this->assertCount(1, $data['results']);
        $this->assertEquals($product2->id_produk, $data['results'][0]['id']);
    }

    /** @test */
    public function regular_user_can_access_transaction_favorites()
    {
        FavoriteProduct::create([
            'product_id' => $this->product->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->user)
            ->get('/transactions/favorites');

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data);
    }

    /** @test */
    public function favorites_are_ordered_correctly()
    {
        // Create products with specific order
        $products = [];
        for ($i = 1; $i <= 3; $i++) {
            $products[] = Produk::factory()->create([
                'nama_produk' => "Product {$i}",
                'kode_produk' => "PROD{$i}"
            ]);
        }

        // Create favorites in reverse order
        FavoriteProduct::create([
            'product_id' => $products[2]->id_produk,
            'sort_order' => 3,
            'is_active' => true
        ]);

        FavoriteProduct::create([
            'product_id' => $products[0]->id_produk,
            'sort_order' => 1,
            'is_active' => true
        ]);

        FavoriteProduct::create([
            'product_id' => $products[1]->id_produk,
            'sort_order' => 2,
            'is_active' => true
        ]);

        $response = $this->actingAs($this->admin)
            ->get('/transactions/favorites');

        $data = $response->json();

        // Should be ordered by sort_order
        $this->assertEquals('Product 1', $data[0]['nama']);
        $this->assertEquals('Product 2', $data[1]['nama']);
        $this->assertEquals('Product 3', $data[2]['nama']);
    }
}