<?php

namespace App\Exports;

use App\Models\BookType;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BooksTemplateExport implements WithHeadings, WithEvents, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Judul Buku',
            'Jenis Buku',
            'Penerbit',
            'Pengarang',
            'Tahun Perolehan',
            'Asal-Usul',
            'Jumlah',
            'Kondisi',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Book Types Validation (Column B)
                $bookTypes = BookType::pluck('name')->toArray();
                if (!empty($bookTypes)) {
                    $typeList = '"' . implode(',', $bookTypes) . '"';

                    $validation = $event->sheet->getCell('B2')->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input Error');
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pilih Jenis Buku');
                    $validation->setPrompt('Pilih dari daftar.');
                    $validation->setFormula1($typeList);

                    // Apply to a range
                    $event->sheet->setDataValidation('B2:B100', $validation);
                }

                // Condition Validation (Column H)
                $conditions = ['Laik', 'Tidak Laik'];
                $conditionList = '"' . implode(',', $conditions) . '"';

                $validation2 = $event->sheet->getCell('H2')->getDataValidation();
                $validation2->setType(DataValidation::TYPE_LIST);
                $validation2->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation2->setAllowBlank(false);
                $validation2->setShowInputMessage(true);
                $validation2->setShowErrorMessage(true);
                $validation2->setShowDropDown(true);
                $validation2->setPromptTitle('Pilih Kondisi');
                $validation2->setPrompt('Pilih kondisi buku.');
                $validation2->setFormula1($conditionList);

                $event->sheet->setDataValidation('H2:H100', $validation2);
            },
        ];
    }
}
