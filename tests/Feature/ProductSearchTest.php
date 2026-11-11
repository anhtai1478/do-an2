<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_returns_products_whose_name_contains_the_keyword(): void
    {
        Product::factory()->create(['name' => 'test']);
        Product::factory()->create(['name' => 'Blue Jeans']);
        Product::factory()->create(['name' => 'Xyzwq Product']);

        $this->get(route('frontend.search', ['name' => 'e']))
            ->assertOk()
            ->assertViewIs('frontend.search.index')
            ->assertSeeText('test')
            ->assertSeeText('Blue Jeans')
            ->assertDontSeeText('Xyzwq Product');
    }

    public function test_search_shows_empty_message_when_nothing_matches(): void
    {
        Product::factory()->create(['name' => 'Xyzwq Product']);

        $this->get(route('frontend.search', ['name' => 'zzz']))
            ->assertOk()
            ->assertSeeText('Không tìm thấy sản phẩm nào');
    }

    public function test_search_without_keyword_shows_all_products(): void
    {
        Product::factory()->create(['name' => 'Xyzwq Product']);
        Product::factory()->create(['name' => 'Qwvx Item']);

        $this->get(route('frontend.search'))
            ->assertOk()
            ->assertSeeText('Xyzwq Product')
            ->assertSeeText('Qwvx Item');
    }
}
