<?php

namespace Tests\Feature;

use App\Models\PublicPage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicPageManagementTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dynamic_menu_and_submenu_are_visible_on_public_home(): void
    {
        $parent = PublicPage::factory()->create(['title' => 'Menu Penelitian Dinamis', 'slug' => 'menu-penelitian-dinamis']);
        $child = PublicPage::factory()->create(['parent_id' => $parent->id, 'title' => 'Submenu Roadmap Dinamis', 'slug' => 'submenu-roadmap-dinamis']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($parent->title)
            ->assertSee($child->title)
            ->assertSee(route('public-pages.show', $child), false);
    }

    public function test_published_page_is_public_and_draft_page_returns_not_found(): void
    {
        $published = PublicPage::factory()->create(['slug' => 'halaman-publik-test', 'content' => 'Isi halaman publik dinamis.']);
        $draft = PublicPage::factory()->create(['slug' => 'halaman-draft-test', 'is_published' => false]);

        $this->get(route('public-pages.show', $published))->assertOk()->assertSee('Isi halaman publik dinamis.');
        $this->get(route('public-pages.show', $draft))->assertNotFound();
    }

    public function test_admin_can_create_and_update_dynamic_public_page(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $payload = $this->validPayload(['title' => 'Panduan Dinamis', 'slug' => 'panduan-dinamis']);

        $this->actingAs($admin)->post(route('public-pages.store'), $payload)->assertRedirect();
        $page = PublicPage::query()->where('slug', 'panduan-dinamis')->firstOrFail();
        $this->assertSame($admin->id, $page->updated_by);

        $this->actingAs($admin)->put(route('public-pages.update', $page), $this->validPayload([
            'title' => 'Panduan Dinamis Diperbarui',
            'slug' => 'panduan-dinamis',
            'is_published' => 0,
        ]))->assertRedirect();

        $this->assertDatabaseHas('public_pages', ['id' => $page->id, 'title' => 'Panduan Dinamis Diperbarui', 'is_published' => false]);
    }

    public function test_non_admin_cannot_manage_dynamic_public_pages(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);
        $page = PublicPage::factory()->create();

        $this->actingAs($lecturer)->get(route('public-pages.index'))->assertForbidden();
        $this->actingAs($lecturer)->post(route('public-pages.store'), $this->validPayload())->assertForbidden();
        $this->actingAs($lecturer)->delete(route('public-pages.destroy', $page))->assertForbidden();
    }

    public function test_admin_can_upload_an_editor_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);

        $response = $this->actingAs($admin)->postJson(route('public-pages.images.store'), [
            'image' => UploadedFile::fake()->image('kegiatan-uppm.jpg', 1200, 800),
        ]);

        $response->assertOk()->assertJsonStructure(['url']);
        $path = str($response->json('url'))->after('/storage/')->toString();
        Storage::disk('public')->assertExists($path);
    }

    public function test_cover_image_is_stored_and_rich_content_is_sanitized(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        $payload = $this->validPayload([
            'slug' => 'halaman-kaya-visual',
            'featured_image' => UploadedFile::fake()->image('sampul.webp', 1200, 800),
            'content' => '<h2>Judul aman</h2><div><script>alert("bahaya")</script></div><p onclick="bahaya()"><strong>Isi tebal</strong></p>',
        ]);

        $this->actingAs($admin)->post(route('public-pages.store'), $payload)->assertRedirect();

        $page = PublicPage::query()->where('slug', 'halaman-kaya-visual')->firstOrFail();
        Storage::disk('public')->assertExists($page->featured_image_path);
        $this->assertStringContainsString('<h2>Judul aman</h2>', $page->content);
        $this->assertStringContainsString('<strong>Isi tebal</strong>', $page->content);
        $this->assertStringNotContainsString('<script', $page->content);
        $this->assertStringNotContainsString('onclick', $page->content);

        $this->get(route('public-pages.show', $page))
            ->assertOk()
            ->assertSee('<h2>Judul aman</h2>', false)
            ->assertDontSee('<script', false);
    }

    public function test_non_admin_cannot_upload_an_editor_image(): void
    {
        Storage::fake('public');
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->postJson(route('public-pages.images.store'), [
            'image' => UploadedFile::fake()->image('tidak-diizinkan.jpg'),
        ])->assertForbidden();
    }

    /** @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'parent_id' => null,
            'title' => 'Halaman UPPM Baru',
            'slug' => 'halaman-uppm-baru',
            'summary' => 'Ringkasan halaman baru.',
            'content' => 'Isi lengkap halaman baru yang dikelola administrator.',
            'sort_order' => 1,
            'is_published' => 1,
            ...$overrides,
        ];
    }
}
