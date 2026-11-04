<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicHomePageTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_home_page_uses_persisted_site_content(): void
    {
        $settings = SiteSetting::query()->updateOrCreate(['id' => 1], [
            ...SiteSetting::defaults(),
            'hero_title' => 'Judul Beranda UPPM Terbaru',
            'announcement_title' => 'Informasi Hibah Terbaru',
            'is_announcement_active' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($settings->hero_title)
            ->assertSee($settings->announcement_title)
            ->assertSee('Masuk Portal');
    }

    public function test_inactive_announcement_is_not_visible_publicly(): void
    {
        SiteSetting::query()->updateOrCreate(['id' => 1], [
            ...SiteSetting::defaults(),
            'announcement_title' => 'Informasi Yang Disembunyikan',
            'is_announcement_active' => false,
        ]);

        $this->get(route('home'))->assertOk()->assertDontSee('Informasi Yang Disembunyikan');
    }

    public function test_admin_can_update_public_home_page_settings(): void
    {
        $admin = User::factory()->create(['roles' => ['admin_uppm']]);
        SiteSetting::query()->updateOrCreate(['id' => 1], SiteSetting::defaults());

        $response = $this->actingAs($admin)->put(route('site-settings.update'), $this->validPayload([
            'hero_title' => 'Beranda Baru dari Administrator',
            'is_announcement_active' => 1,
        ]));

        $response->assertRedirect()->assertSessionHas('success');
        $this->assertDatabaseHas('site_settings', [
            'id' => 1,
            'hero_title' => 'Beranda Baru dari Administrator',
            'is_announcement_active' => true,
            'updated_by' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_open_or_update_site_settings(): void
    {
        $lecturer = User::factory()->create(['roles' => ['dosen']]);

        $this->actingAs($lecturer)->get(route('site-settings.edit'))->assertForbidden();
        $this->actingAs($lecturer)->put(route('site-settings.update'), $this->validPayload())->assertForbidden();
    }

    /** @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'site_name' => 'SIM UPPM Kampus SBH',
            'tagline' => 'Penelitian dan pengabdian',
            'hero_title' => 'Beranda resmi UPPM',
            'hero_description' => 'Portal informasi penelitian dan pengabdian kepada masyarakat.',
            'about_title' => 'Tentang UPPM',
            'about_content' => 'Unit yang mengelola penelitian dan pengabdian kepada masyarakat.',
            'announcement_title' => 'Pengumuman terbaru',
            'announcement_content' => 'Periode pengajuan hibah internal telah dibuka.',
            'announcement_link' => 'https://kampus.example.test/informasi',
            'contact_email' => 'uppm@kampus.test',
            'contact_phone' => '081234567890',
            'address' => 'Kampus SBH',
            ...$overrides,
        ];
    }
}
