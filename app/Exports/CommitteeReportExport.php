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

        foreach ($this->reportData as $index => $data) {
            $paymentDates = $data['payments']->count() > 0
                ? $data['payments']->pluck('payment_date')->map(fn($d) => $d->format('d/m/Y'))->implode(', ')
                : '-';

            $rows->push([
                'no' => $index + 1,
                'nis' => $data['student']->nis ?? '-',
                'nama' => $data['student']->name,
                'kelas' => $data['class_name'],
                'tgl_bayar' => $paymentDates,
                'total_bayar' => $data['total_paid'],
                'sisa_bayar' => $data['remaining'],
                'status' => $data['is_paid_full'] ? 'LUNAS' : 'BELUM LUNAS',
            ]);
        }

        // Add summary row
        $rows->push([
            'no' => '',
            'nis' => '',
            'nama' => '',
            'kelas' => '',
            'tgl_bayar' => 'TOTAL',
            'total_bayar' => $this->summary['total_terbayar'],
            'sisa_bayar' => $this->summary['total_sisa'],
            'status' => '',
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama',
            'Kelas',
            'Tgl Bayar',
            'Total Bayar',
            'Sisa Bayar',
            'Status',
        ];
    }

    public function title(): string
    {
        return 'Laporan Dana Komite';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->reportData) + $this->headerRowCount + 1;

        return [
            // Header row style
            $this->headerRowCount => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A5F'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Total row style
            $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

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
                $sheet->setCellValue('A1', $this->school->name ?? 'SEKOLAH');
                $sheet->setCellValue('A2', $this->school->address ?? '');
                $sheet->setCellValue('A3', '');
                $sheet->setCellValue('A4', 'LAPORAN PEMBAYARAN DANA KOMITE');
                $sheet->setCellValue('A5', $periodText . ' | Kelas: ' . $this->schoolClass->name);

                // Merge cells for header
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A4:H4');
                $sheet->mergeCells('A5:H5');

                // Style header
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(12);
                $sheet->getStyle('A1:A5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Add border to data table
                $lastRow = count($this->reportData) + $this->headerRowCount + 1;
                $sheet->getStyle('A' . $this->headerRowCount . ':H' . $lastRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

                // Format currency columns
                for ($row = $this->headerRowCount + 1; $row <= $lastRow; $row++) {
                    $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0');
                }

                // Set page orientation to portrait and paper size to A4
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

                // Set print area
                $sheet->getPageSetup()->setPrintArea('A1:H' . $lastRow);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
