<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.categories.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.categories.create'))->assertOk();
        $this->actingAs($admin)->post(route('admin.categories.store'), ['nama_kategori' => 'Perundungan'])->assertSessionHasNoErrors();

        $category = Category::firstWhere('nama_kategori', 'Perundungan');
        $this->actingAs($admin)->get(route('admin.categories.edit', $category))->assertOk();
        $this->actingAs($admin)->put(route('admin.categories.update', $category), ['nama_kategori' => 'Perundungan Siber'])->assertSessionHasNoErrors();
        $this->assertSame('Perundungan Siber', $category->fresh()->nama_kategori);

        $this->actingAs($admin)->patch(route('admin.categories.toggle', $category));
        $this->assertFalse($category->fresh()->is_active);
        $this->assertDatabaseCount('categories', 1);
    }

    public function test_report_options_only_contain_active_categories(): void
    {
        $active = Category::factory()->create();
        $inactive = Category::factory()->create(['is_active' => false]);

        $this->assertTrue(Category::optionsForReport()->contains($active));
        $this->assertFalse(Category::optionsForReport()->contains($inactive));
        $this->assertTrue(Category::optionsForReport($inactive->id)->contains($inactive));
    }

    public function test_duplicate_and_forbidden(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['nama_kategori' => 'Kekerasan Fisik']);

        $this->actingAs($admin)->post(route('admin.categories.store'), ['nama_kategori' => 'Kekerasan Fisik'])->assertSessionHasErrors('nama_kategori');
        $this->actingAs(User::factory()->create())->get(route('admin.categories.index'))->assertForbidden();
    }
}
