<?php

namespace App\Imports;

use App\Models\SchoolClass;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $classes;

    public function __construct()
    {
        // Cache classes to avoid querying DB for every row
        $this->classes = SchoolClass::all()->pluck('id', 'name');
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find class ID by name (insensitive search could be added if needed)
        $className = trim($row['kelas']);
        $classId = $this->classes[$className] ?? null;

        if (!$classId) {
            // Option: Create class if not exists? Or just skip/error. 
            // For now, let's assume class MUST exist.
            // In a real app, we might throw an exception or return null.
             return null; 
        }

        // Normalize Gender
        $gender = strtolower($row['jenis_kelamin']); // l/p/laki-laki/perempuan/male/female
        if (in_array($gender, ['l', 'laki-laki', 'male'])) {
            $gender = 'male';
        } elseif (in_array($gender, ['p', 'perempuan', 'female'])) {
            $gender = 'female';
        }

        // NISN mapping
        $nisn = isset($row['nisn']) ? trim($row['nisn']) : null;
        
        // Tanggal Lahir mapping
        $tanggalLahir = null;
        if (!empty($row['tanggal_lahir'])) {
            // Excel pure date value is typically numeric
            if (is_numeric($row['tanggal_lahir'])) {
                try {
                    $tanggalLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tanggalLahir = null;
                }
            } else {
                // assume text d/m/Y
                try {
                    $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', trim($row['tanggal_lahir']))->format('Y-m-d');
                } catch (\Exception $e) {
                    try {
                        // fallback to Y-m-d
                        $tanggalLahir = \Carbon\Carbon::parse(trim($row['tanggal_lahir']))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $tanggalLahir = null;
                    }
                }
            }
        }

        return new Student([
            'name'            => $row['nama_siswa'],
            'nis'             => $row['nis'] ?? null,
            'nisn'            => $nisn,
            'tanggal_lahir'   => $tanggalLahir,
            'gender'          => $gender,
            'school_class_id' => $classId,
            'enrollment_year' => $row['tahun_masuk'] ?? date('Y'),
            'is_active'       => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_siswa' => 'required',
            'kelas' => ['required', function($attribute, $value, $onFailure) {
                if (!isset($this->classes[trim($value)])) {
                     $onFailure('Kelas "' . $value . '" tidak ditemukan di database.');
                }
            }],
            'jenis_kelamin' => 'required|in:L,P,Laki-laki,Perempuan,Male,Female,l,p,laki-laki,perempuan,male,female',
            'tahun_masuk' => 'required|numeric|digits:4',
        ];
    }
}
