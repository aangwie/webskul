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

        return new Student([
            'name'            => $row['nama_siswa'],
            'nis'             => $row['nis'] ?? null,
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
