<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

/**
 * PBI-02: Kelola kategori kekerasan.
 */
class CategoryTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_admin_can_add_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visitRoute('admin.categories.index')
                ->clickAndWaitForReload('@add-category')
                ->type('@nama_kategori', 'Kekerasan Psikis')
                ->clickAndWaitForReload('@save-category')
                ->assertRouteIs('admin.categories.index')
                ->assertSee('Kategori berhasil ditambahkan.')
                ->assertSeeIn('@category-table', 'Kekerasan Psikis');
        });
    }

    public function test_category_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['nama_kategori' => 'Perundungan']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visitRoute('admin.categories.create')
                ->type('@nama_kategori', 'Perundungan')
                ->clickAndWaitForReload('@save-category')
                ->assertRouteIs('admin.categories.create')
                ->assertPresent('@error-nama_kategori');
        });
    }

    public function test_admin_can_edit_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['nama_kategori' => 'Kategori Lama']);

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->loginAs($admin)
                ->visitRoute('admin.categories.index')
                ->clickAndWaitForReload('@edit-category-'.$category->id)
                ->clear('@nama_kategori')
                ->type('@nama_kategori', 'Kategori Baru')
                ->clickAndWaitForReload('@save-category')
                ->assertSee('Kategori berhasil diperbarui.')
                ->assertSeeIn('@category-table', 'Kategori Baru');
        });
    }

    public function test_admin_can_deactivate_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->browse(function (Browser $browser) use ($admin, $category) {
            $browser->loginAs($admin)
                ->visitRoute('admin.categories.index')
                ->click('@toggle-category-'.$category->id)
                ->waitForDialog()->acceptDialog()
                ->waitForText('Kategori dinonaktifkan.')
                ->assertSeeIn('@category-row-'.$category->id, 'Nonaktif');
        });

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_non_admin_cannot_open_category_page(): void
    {
        $mahasiswa = User::factory()->create();

        $this->browse(function (Browser $browser) use ($mahasiswa) {
            $browser->loginAs($mahasiswa)
                ->visitRoute('admin.categories.index')
                ->assertSee('403');
        });
    }
}
