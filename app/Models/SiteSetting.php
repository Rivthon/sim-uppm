<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['site_name', 'tagline', 'hero_title', 'hero_description', 'about_title', 'about_content', 'announcement_title', 'announcement_content', 'announcement_link', 'is_announcement_active', 'contact_email', 'contact_phone', 'address', 'updated_by'])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'site_name' => 'SIM UPPM Kampus SBH',
            'tagline' => 'Penelitian tertata, pengabdian berdampak',
            'hero_title' => 'Bersama membangun riset dan pengabdian yang bermakna.',
            'hero_description' => 'Portal resmi Unit Penelitian dan Pengabdian kepada Masyarakat Kampus SBH untuk informasi, pengajuan, pemantauan, dan luaran kegiatan.',
            'about_title' => 'UPPM Kampus SBH',
            'about_content' => 'UPPM mengelola, mengembangkan, dan mendokumentasikan kegiatan penelitian serta pengabdian kepada masyarakat secara transparan dan berkelanjutan.',
            'announcement_title' => 'Pengajuan hibah internal dibuka',
            'announcement_content' => 'Dosen dapat masuk menggunakan akun PAS untuk melihat periode hibah dan mengajukan proposal Penelitian atau PkM.',
            'announcement_link' => null,
            'is_announcement_active' => true,
            'contact_email' => 'uppm@kampus.ac.id',
            'contact_phone' => null,
            'address' => 'Kampus SBH',
        ];
    }

    protected function casts(): array
    {
        return ['is_announcement_active' => 'boolean'];
    }
}
