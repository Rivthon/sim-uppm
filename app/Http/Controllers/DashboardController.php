<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $modules = [
            ['name' => 'Master Data', 'description' => 'Dosen, mahasiswa, prodi, kepakaran, dan tahun akademik.', 'icon' => 'database', 'status' => 'Fondasi'],
            ['name' => 'Renstra & Roadmap', 'description' => 'Arah penelitian dan PkM institusi, prodi, serta dosen.', 'icon' => 'route', 'status' => 'Perencanaan'],
            ['name' => 'Program & Hibah', 'description' => 'Periode, skema, persyaratan, jadwal, dan pagu pendanaan.', 'icon' => 'award', 'status' => 'Pengelolaan'],
            ['name' => 'Proposal', 'description' => 'Pengajuan penelitian dan PkM beserta tim, RAB, dan dokumen.', 'icon' => 'file', 'status' => 'Pengusulan'],
            ['name' => 'Reviewer', 'description' => 'Penugasan, rubrik penilaian, skor, dan konflik kepentingan.', 'icon' => 'users', 'status' => 'Seleksi'],
            ['name' => 'Kontrak & Pendanaan', 'description' => 'SK, kontrak, termin pencairan, dan realisasi anggaran.', 'icon' => 'wallet', 'status' => 'Pelaksanaan'],
            ['name' => 'Monitoring & Evaluasi', 'description' => 'Logbook, laporan kemajuan, monev, dan tindak lanjut.', 'icon' => 'activity', 'status' => 'Pengawasan'],
            ['name' => 'Laporan & Luaran', 'description' => 'Laporan akhir, publikasi, HKI, buku, produk, dan bukti.', 'icon' => 'chart', 'status' => 'Capaian'],
        ];

        $proposalQuery = Proposal::query()
            ->when(! auth()->user()->hasRole('admin_uppm'), fn ($query) => $query->whereBelongsTo(auth()->user(), 'lead'));

        $stats = [
            'proposals' => (clone $proposalQuery)->count(),
            'submitted' => (clone $proposalQuery)->where('status', 'submitted')->count(),
        ];

        return view('dashboard', compact('modules', 'stats'));
    }
}
