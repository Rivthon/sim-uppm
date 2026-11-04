<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\GrantPeriod;
use App\Models\GrantScheme;
use App\Models\Proposal;
use App\Models\ResearchField;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturers = collect([
            ['pas_user_id' => 'PAS-DOSEN-001', 'username' => 'dosen1', 'name' => 'Dr. Andi Saputra', 'email' => 'dosen1@kampus.test', 'identity_number' => '0123456701', 'study_program' => 'Teknik Informatika'],
            ['pas_user_id' => 'PAS-DOSEN-002', 'username' => 'dosen2', 'name' => 'Dr. Siti Rahma', 'email' => 'dosen2@kampus.test', 'identity_number' => '0123456702', 'study_program' => 'Sistem Informasi'],
        ])->map(fn (array $attributes): User => $this->createUser($attributes, ['dosen', 'reviewer']));

        $students = collect([
            ['pas_user_id' => 'PAS-MHS-001', 'username' => 'mahasiswa1', 'name' => 'Budi Santoso', 'email' => 'mahasiswa1@kampus.test', 'identity_number' => '20260001', 'study_program' => 'Teknik Informatika'],
            ['pas_user_id' => 'PAS-MHS-002', 'username' => 'mahasiswa2', 'name' => 'Citra Lestari', 'email' => 'mahasiswa2@kampus.test', 'identity_number' => '20260002', 'study_program' => 'Teknik Informatika'],
            ['pas_user_id' => 'PAS-MHS-003', 'username' => 'mahasiswa3', 'name' => 'Deni Pratama', 'email' => 'mahasiswa3@kampus.test', 'identity_number' => '20260003', 'study_program' => 'Sistem Informasi'],
            ['pas_user_id' => 'PAS-MHS-004', 'username' => 'mahasiswa4', 'name' => 'Eka Wulandari', 'email' => 'mahasiswa4@kampus.test', 'identity_number' => '20260004', 'study_program' => 'Sistem Informasi'],
        ])->map(fn (array $attributes): User => $this->createUser($attributes, ['mahasiswa']));

        $period = GrantPeriod::updateOrCreate(['name' => 'Hibah Internal Demo 2026/2027'], [
            'academic_year_id' => AcademicYear::query()->where('name', '2026/2027')->value('id'),
            'grant_scheme_id' => GrantScheme::query()->where('code', 'PDP')->value('id'),
            'submission_starts_at' => '2026-08-01 00:00:00',
            'submission_ends_at' => '2027-05-31 23:59:59',
            'quota' => 20,
            'budget' => 300000000,
            'requirements' => 'Periode hibah untuk demonstrasi akun dosen dan mahasiswa.',
            'status' => 'open',
            'created_by' => $lecturers[0]->id,
        ]);

        foreach ($lecturers as $index => $lecturer) {
            $proposal = Proposal::updateOrCreate(['code' => 'DEMO-UPPM-00'.($index + 1)], [
                'grant_period_id' => $period->id,
                'research_field_id' => ResearchField::query()->where('code', $index === 0 ? 'TIK' : 'SOS')->value('id'),
                'lead_id' => $lecturer->id,
                'title' => $index === 0 ? 'Sistem Monitoring Penelitian Kampus' : 'Peningkatan Literasi Digital Masyarakat',
                'type' => 'penelitian',
                'abstract' => 'Proposal dummy untuk demonstrasi dashboard dosen dan mahasiswa.',
                'background' => 'Data dummy disediakan agar seluruh peran dapat mencoba alur aplikasi.',
                'objectives' => 'Mendemonstrasikan akses proposal berdasarkan keanggotaan tim.',
                'methodology' => 'Observasi, pengembangan, pelaksanaan, dan evaluasi.',
                'roadmap_alignment' => 'Selaras dengan roadmap penelitian dan transformasi digital kampus.',
                'requested_budget' => 10000000,
                'duration_months' => 12,
                'status' => 'draft',
            ]);

            $proposal->members()->where('member_type', 'mahasiswa')->delete();

            foreach ($students->slice($index * 2, 2)->values() as $sortOrder => $student) {
                $proposal->members()->create([
                    'user_id' => $student->id,
                    'name' => $student->name,
                    'identity_number' => $student->identity_number,
                    'member_type' => 'mahasiswa',
                    'study_program' => $student->study_program,
                    'sort_order' => $sortOrder,
                ]);
            }
        }
    }

    /**
     * @param  array<string, string>  $attributes
     * @param  array<int, string>  $roles
     */
    private function createUser(array $attributes, array $roles): User
    {
        $user = User::updateOrCreate(['pas_user_id' => $attributes['pas_user_id']], [
            ...$attributes,
            'unit' => 'Kampus SBH',
            'roles' => $roles,
            'account_source' => 'pas',
            'is_active' => true,
            'pas_synced_at' => now(),
        ]);

        $user->forceFill(['password' => Hash::make('password123')])->save();

        return $user;
    }
}
