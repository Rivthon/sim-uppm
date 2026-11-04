<?php

namespace App\Http\Controllers;

use App\Models\FinalReport;
use App\Models\ResearchOutput;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AccreditationController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasRole('admin_uppm'), 403);
        $year = max(2020, min(2100, $request->integer('year', now()->year)));
        $evidence = $this->evidence($year);
        $summary = $evidence->countBy('category');

        return view('accreditation.index', compact('year', 'evidence', 'summary'));
    }

    public function export(Request $request): StreamedResponse
    {
        abort_unless($request->user()->hasRole('admin_uppm'), 403);
        $year = max(2020, min(2100, $request->integer('year', now()->year)));
        $evidence = $this->evidence($year);

        return response()->streamDownload(function () use ($evidence): void {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['Kategori', 'Jenis', 'Judul Kegiatan', 'Judul Bukti', 'Dosen', 'Tanggal', 'Status']);
            foreach ($evidence as $item) {
                fputcsv($stream, [$item['category'], $item['type'], $item['proposal'], $item['title'], $item['lecturer'], $item['date'], $item['status']]);
            }
            fclose($stream);
        }, "rekap-akreditasi-{$year}.csv", ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return Collection<int, array{category: string, type: string, proposal: string, title: string, lecturer: string, date: string, status: string, url: string}> */
    private function evidence(int $year): Collection
    {
        $outputs = ResearchOutput::query()->where('verification_status', 'verified')->whereYear('verified_at', $year)
            ->with(['contract.proposal.lead'])->latest('verified_at')->get()->map(fn (ResearchOutput $output): array => [
                'category' => 'Luaran', 'type' => ucfirst($output->type), 'proposal' => $output->contract->proposal->title,
                'title' => $output->title, 'lecturer' => $output->contract->proposal->lead->name,
                'date' => $output->verified_at->format('Y-m-d'), 'status' => 'Terverifikasi', 'url' => route('reporting.documents.output', $output),
            ]);
        $reports = FinalReport::query()->where('status', 'verified')->whereYear('verified_at', $year)
            ->with(['contract.proposal.lead'])->latest('verified_at')->get()->map(fn (FinalReport $report): array => [
                'category' => 'Laporan Akhir', 'type' => ucfirst($report->contract->proposal->type), 'proposal' => $report->contract->proposal->title,
                'title' => 'Laporan akhir '.$report->contract->contract_number, 'lecturer' => $report->contract->proposal->lead->name,
                'date' => $report->verified_at->format('Y-m-d'), 'status' => 'Terverifikasi', 'url' => route('reporting.documents.final-report', $report),
            ]);

        return $outputs->concat($reports)->sortByDesc('date')->values();
    }
}
