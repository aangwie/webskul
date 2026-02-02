<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\CommitteeFee;
use App\Models\CommitteePayment;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class CommitteeReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents, ShouldAutoSize
{
    protected $filterType;
    protected $academicYear;
    protected $dateFrom;
    protected $dateTo;
    protected $schoolClassId;
    protected $reportType;
    protected $reportData;
    protected $summary;
    protected $schoolClass;
    protected $school;
    protected $headerRowCount = 6;

    public function __construct($params)
    {
        $this->filterType = $params['filter_type'];
        $this->academicYear = $params['academic_year'] ?? null;
        $this->dateFrom = $params['date_from'] ?? null;
        $this->dateTo = $params['date_to'] ?? null;
        $this->schoolClassId = $params['school_class_id'];
        $this->reportType = $params['report_type'];
        $this->school = \App\Models\SchoolProfile::first();

        $this->generateReportData();
    }

    protected function generateReportData()
    {
        // Get classes
        if ($this->schoolClassId == 'all') {
            $classes = SchoolClass::where('is_active', true)->ordered()->get();
            $this->schoolClass = (object) ['name' => 'Semua Kelas'];
        } else {
            $class = SchoolClass::findOrFail($this->schoolClassId);
            $classes = collect([$class]);
            $this->schoolClass = $class;
        }

        $this->reportData = [];
        $totalTagihan = 0;
        $totalTerbayar = 0;
        $totalStudentsCount = 0;

        if ($this->reportType == 'class_summary' || $this->reportType == 'all_summary') {
            foreach ($classes as $class) {
                $studentIds = Student::where('school_class_id', $class->id)->pluck('id');

                if ($this->filterType === 'academic_year') {
                    $fee = CommitteeFee::where('academic_year_id', $this->academicYear->id)
                        ->where('school_class_id', $class->id)
                        ->first();

                    $totalPaid = CommitteePayment::whereIn('student_id', $studentIds)
                        ->whereHas('committeeFee', function ($q) {
                            $q->where('academic_year_id', $this->academicYear->id);
                        })->sum('amount');

                    $totalStudents = $studentIds->count();
                    $totalTarget = $fee ? $fee->amount * $totalStudents : 0;
                } else {
                    $totalPaid = CommitteePayment::whereIn('student_id', $studentIds)
                        ->whereBetween('payment_date', [$this->dateFrom, $this->dateTo])
                        ->sum('amount');

                    $totalStudents = $studentIds->count();
                    $fee = CommitteeFee::where('school_class_id', $class->id)
                        ->latest('created_at')
                        ->first();
                    $totalTarget = $fee ? $fee->amount * $totalStudents : 0;
                }

                $this->reportData[] = [
                    'class_name' => $class->name,
                    'total_students' => $totalStudents,
                    'total_target' => $totalTarget,
                    'total_paid' => $totalPaid,
                    'remaining' => max(0, $totalTarget - $totalPaid),
                ];

                $totalTagihan += $totalTarget;
                $totalTerbayar += $totalPaid;
                $totalStudentsCount += $totalStudents;
            }
        } else {
            // detail or recapitulation
            foreach ($classes as $class) {
                if ($this->filterType === 'academic_year') {
                    $fee = CommitteeFee::where('academic_year_id', $this->academicYear->id)
                        ->where('school_class_id', $class->id)
                        ->first();
                } else {
                    $fee = CommitteeFee::where('school_class_id', $class->id)
                        ->latest('created_at')
                        ->first();
                }

                if (!$fee)
                    continue;

                $students = Student::where('school_class_id', $class->id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();

                foreach ($students as $student) {
                    if ($this->filterType === 'academic_year') {
                        $payments = CommitteePayment::where('student_id', $student->id)
                            ->where('committee_fee_id', $fee->id)
                            ->orderBy('payment_date', 'asc')
                            ->get();
                    } else {
                        $payments = CommitteePayment::where('student_id', $student->id)
                            ->whereBetween('payment_date', [$this->dateFrom, $this->dateTo])
                            ->orderBy('payment_date', 'asc')
                            ->get();
                    }

                    $totalPaid = $payments->sum('amount');
                    $remaining = $fee->amount - $totalPaid;

                    $this->reportData[] = [
                        'student' => $student,
                        'class_name' => $class->name,
                        'fee_amount' => $fee->amount,
                        'payments' => $payments,
                        'total_paid' => $totalPaid,
                        'remaining' => max(0, $remaining),
                        'is_paid_full' => $totalPaid >= $fee->amount,
                    ];

                    $totalTagihan += $fee->amount;
                    $totalTerbayar += $totalPaid;
                }
                $totalStudentsCount += $students->count();
            }
        }

        $this->summary = [
            'total_students' => $totalStudentsCount,
            'total_tagihan' => $totalTagihan,
            'total_terbayar' => $totalTerbayar,
            'total_sisa' => max(0, $totalTagihan - $totalTerbayar),
        ];
    }

    public function collection(): Collection
    {
        $rows = new Collection();

        if ($this->reportType == 'class_summary') {
            foreach ($this->reportData as $index => $data) {
                $rows->push([
                    'no' => $index + 1,
                    'nama_kelas' => $data['class_name'],
                    'total_siswa' => $data['total_students'],
                    'total_tagihan' => $data['total_target'],
                    'total_terbayar' => $data['total_paid'],
                    'sisa_tagihan' => $data['remaining'],
                ]);
            }
            // Add summary row
            $rows->push([
                'no' => '',
                'nama_kelas' => 'TOTAL GABUNGAN',
                'total_siswa' => $this->summary['total_students'],
                'total_tagihan' => $this->summary['total_tagihan'],
                'total_terbayar' => $this->summary['total_terbayar'],
                'sisa_tagihan' => $this->summary['total_sisa'],
            ]);
        } elseif ($this->reportType == 'all_summary') {
            $rows->push([
                'item' => 'Total Siswa (Semua Kelas)',
                'nilai' => $this->summary['total_students'] . ' Siswa',
            ]);
            $rows->push([
                'item' => 'Total Target Tagihan',
                'nilai' => $this->summary['total_tagihan'],
            ]);
            $rows->push([
                'item' => 'Total Pembayaran Masuk',
                'nilai' => $this->summary['total_terbayar'],
            ]);
            $rows->push([
                'item' => 'Total Sisa Tagihan',
                'nilai' => $this->summary['total_sisa'],
            ]);
            $totalPercent = $this->summary['total_tagihan'] > 0 ? ($this->summary['total_terbayar'] / $this->summary['total_tagihan']) * 100 : 0;
            $rows->push([
                'item' => 'Persentase Pelunasan',
                'nilai' => number_format($totalPercent, 1) . '%',
            ]);
        } else {
            // detail or recapitulation
            foreach ($this->reportData as $index => $data) {
                $rowData = [
                    'no' => $index + 1,
                    'nis' => $data['student']->nis ?? '-',
                    'nama' => $data['student']->name,
                    'kelas' => $data['class_name'],
                ];

                if ($this->reportType == 'detail') {
                    $paymentDates = $data['payments']->count() > 0
                        ? $data['payments']->pluck('payment_date')->map(fn($d) => $d->format('d/m/Y'))->implode(', ')
                        : '-';
                    $rowData['tgl_bayar'] = $paymentDates;
                }

                $rowData['total_bayar'] = $data['total_paid'];
                $rowData['sisa_bayar'] = $data['remaining'];
                $rowData['status'] = $data['is_paid_full'] ? 'LUNAS' : 'BELUM';

                $rows->push($rowData);
            }

            // Add summary row
            $summaryRow = [
                'no' => '',
                'nis' => '',
                'nama' => '',
                'kelas' => 'TOTAL',
            ];
            if ($this->reportType == 'detail') {
                $summaryRow['tgl_bayar'] = '';
            }
            $summaryRow['total_bayar'] = $this->summary['total_terbayar'];
            $summaryRow['sisa_bayar'] = $this->summary['total_sisa'];
            $summaryRow['status'] = '';

            $rows->push($summaryRow);
        }

        return $rows;
    }

    public function headings(): array
    {
        if ($this->reportType == 'class_summary') {
            return ['No', 'Kelas', 'Siswa', 'Tagihan', 'Terbayar', 'Sisa'];
        } elseif ($this->reportType == 'all_summary') {
            return ['Item Ringkasan', 'Nilai'];
        } elseif ($this->reportType == 'recapitulation') {
            return ['No', 'NIS', 'Nama', 'Kelas', 'Terbayar', 'Sisa', 'Status'];
        } else {
            // detail
            return ['No', 'NIS', 'Nama', 'Kelas', 'Tgl Bayar', 'Total Bayar', 'Sisa Bayar', 'Status'];
        }
    }

    public function title(): string
    {
        return 'Laporan Dana Komite';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $styles = [
            // Header row style
            $this->headerRowCount => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A5F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Summary row style
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF'],
                ],
            ],
        ];

        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $highestCol = $sheet->getHighestColumn();

                // Get filter period text
                if ($this->filterType === 'academic_year' && $this->academicYear) {
                    $periodText = 'Tahun Ajaran: ' . $this->academicYear->year;
                } else {
                    $periodText = 'Periode: ' . \Carbon\Carbon::parse($this->dateFrom)->format('d/m/Y') .
                        ' - ' . \Carbon\Carbon::parse($this->dateTo)->format('d/m/Y');
                }

                // Insert header rows at top
                $sheet->insertNewRowBefore(1, $this->headerRowCount - 1);

                // Set header content
                $sheet->setCellValue('A1', $this->school->name ?? 'SMP NEGERI 6 SUDIMORO');
                $sheet->setCellValue('A2', $this->school->address ?? '');
                $sheet->setCellValue('A3', '');

                $titlePrefix = 'LAPORAN ' . strtoupper($this->reportType == 'detail' ? 'Detail' : ($this->reportType == 'class_summary' ? 'Rekap Per Kelas' : ($this->reportType == 'all_summary' ? 'Rekap Semua Kelas' : 'Rekapitulasi')));
                $sheet->setCellValue('A4', $titlePrefix . ' PEMBAYARAN DANA KOMITE');
                $sheet->setCellValue('A5', $periodText . ' | Kelas: ' . $this->schoolClass->name);

                // Merge cells for header
                $sheet->mergeCells("A1:{$highestCol}1");
                $sheet->mergeCells("A2:{$highestCol}2");
                $sheet->mergeCells("A4:{$highestCol}4");
                $sheet->mergeCells("A5:{$highestCol}5");

                // Style header
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle("A1:{$highestCol}5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add border to data table
                $sheet->getStyle("A{$this->headerRowCount}:{$highestCol}{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Number formatting
                if ($this->reportType == 'class_summary') {
                    $sheet->getStyle("D" . ($this->headerRowCount + 1) . ":F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                } elseif ($this->reportType == 'all_summary') {
                    $sheet->getStyle("B" . ($this->headerRowCount + 1) . ":B" . ($lastRow - 1))->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                } elseif ($this->reportType == 'recapitulation') {
                    $sheet->getStyle("E" . ($this->headerRowCount + 1) . ":F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                } else {
                    $sheet->getStyle("F" . ($this->headerRowCount + 1) . ":G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                }

                // Page settings
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
