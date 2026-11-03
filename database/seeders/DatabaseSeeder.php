<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\GrantScheme;
use App\Models\ResearchField;
use App\Models\StudyProgram;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AcademicYear::updateOrCreate(['name' => '2026/2027'], [
            'starts_at' => '2026-08-01', 'ends_at' => '2027-07-31', 'is_active' => true,
        ]);

        foreach ([
            ['code' => 'TI', 'name' => 'Teknik Informatika', 'faculty' => 'Fakultas Teknologi Informasi', 'level' => 'S1'],
            ['code' => 'SI', 'name' => 'Sistem Informasi', 'faculty' => 'Fakultas Teknologi Informasi', 'level' => 'S1'],
        ] as $program) {
            StudyProgram::updateOrCreate(['code' => $program['code']], [...$program, 'is_active' => true]);
        }

        foreach ([
            ['code' => 'TIK', 'name' => 'Teknologi Informasi dan Komunikasi'],
            ['code' => 'SOS', 'name' => 'Sosial, Humaniora, dan Pendidikan'],
            ['code' => 'EKO', 'name' => 'Ekonomi dan Kewirausahaan'],
        ] as $field) {
            ResearchField::updateOrCreate(['code' => $field['code']], [...$field, 'is_active' => true]);
        }

        foreach ([
            ['code' => 'PDP', 'name' => 'Penelitian Dosen Pemula', 'type' => 'penelitian', 'maximum_funding' => 15000000],
            ['code' => 'PKM-I', 'name' => 'Pengabdian kepada Masyarakat Internal', 'type' => 'pkm', 'maximum_funding' => 10000000],
        ] as $scheme) {
            GrantScheme::updateOrCreate(['code' => $scheme['code']], [
                ...$scheme, 'duration_months' => 12, 'is_active' => true,
            ]);
        }
    }
}
