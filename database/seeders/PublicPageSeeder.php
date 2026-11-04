<?php

namespace Database\Seeders;

use App\Models\PublicPage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PublicPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            ['Profil UPPM', 'Profil unit, visi, misi, tugas, dan fungsi UPPM.', "Unit Penelitian dan Pengabdian kepada Masyarakat mengelola dan memfasilitasi pelaksanaan tridharma di bidang penelitian dan pengabdian.\n\nVisi\nMenjadi unit yang unggul dalam pengembangan penelitian dan pengabdian yang relevan, inovatif, dan berdampak.\n\nTugas dan Fungsi\nMenyusun rencana, panduan, fasilitasi pelaksanaan, pemantauan, evaluasi, diseminasi hasil, peningkatan kapasitas dosen, serta dokumentasi kegiatan penelitian dan pengabdian.", []],
            ['Struktur Organisasi UPPM', 'Susunan pengelola dan pembagian fungsi UPPM.', 'Struktur organisasi UPPM terdiri dari pimpinan unit, koordinator bidang penelitian, koordinator bidang pengabdian kepada masyarakat, pengelola administrasi, serta tim pendukung sesuai kebutuhan institusi.', []],
            ['Penelitian', 'Kebijakan, arah, dan panduan pelaksanaan penelitian.', 'Informasi perencanaan, roadmap, dan panduan penelitian dosen Kampus SBH.', [
                ['Rencana Strategis Penelitian', 'Arah strategis pengembangan penelitian institusi.'],
                ['Roadmap Penelitian', 'Tahapan dan fokus prioritas penelitian institusi.'],
                ['Panduan Penelitian', 'Ketentuan pengajuan, pelaksanaan, pelaporan, dan luaran penelitian.'],
            ]],
            ['Pengabdian Kepada Masyarakat', 'Kebijakan, arah, dan panduan pelaksanaan PkM.', 'Informasi perencanaan dan pelaksanaan kegiatan pengabdian kepada masyarakat.', [
                ['Rencana Strategis Pengabdian Kepada Masyarakat', 'Arah strategis pengembangan pengabdian kepada masyarakat.'],
                ['Roadmap Pengabdian Kepada Masyarakat', 'Tahapan dan fokus prioritas pengabdian kepada masyarakat.'],
                ['SOP Pengabdian Kepada Masyarakat', 'Prosedur pengajuan, pelaksanaan, monitoring, dan pelaporan PkM.'],
            ]],
        ];

        foreach ($pages as $order => [$title, $summary, $content, $children]) {
            $parent = PublicPage::query()->updateOrCreate(['slug' => Str::slug($title)], [
                'parent_id' => null, 'title' => $title, 'summary' => $summary, 'content' => $content,
                'sort_order' => $order, 'is_published' => true,
            ]);

            foreach ($children as $childOrder => [$childTitle, $childContent]) {
                PublicPage::query()->updateOrCreate(['slug' => Str::slug($childTitle)], [
                    'parent_id' => $parent->id, 'title' => $childTitle, 'summary' => $childContent,
                    'content' => $childContent, 'sort_order' => $childOrder, 'is_published' => true,
                ]);
            }
        }
    }
}
