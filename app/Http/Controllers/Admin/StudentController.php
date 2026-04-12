<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::with('schoolClass');

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->active();
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        } else {
            // Default to active only if no filter is applied? 
            // The previous code had .active() by default. 
            // But usually we want to see active students. 
            // I'll keep it active by default unless 'all' is requested.
            $query->active();
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('school_class_id', $request->class_id);
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $students = $query->orderBy('name')->paginate(15);
        $classes = SchoolClass::active()->ordered()->get();

        // Statistics
        $stats = [
            'total' => Student::active()->count(),
            'male' => Student::active()->male()->count(),
            'female' => Student::active()->female()->count(),
        ];

        return view('admin.students.index', compact('students', 'classes', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SchoolClass::active()->ordered()->get();
        $currentYear = date('Y');
        
        return view('admin.students.create', compact('classes', 'currentYear'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date_format:d/m/Y',
            'enrollment_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'is_active' => 'required|boolean',
            'status_lulus' => 'nullable|in:lulus,tidak_lulus',
            'ijazah_file' => 'nullable|mimes:pdf|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            $validated['status_lulus'] = null;
        }

        if (!$validated['is_active'] && isset($validated['status_lulus']) && $validated['status_lulus'] === 'lulus') {
            if ($request->hasFile('ijazah_file')) {
                $validated['ijazah_file'] = $request->file('ijazah_file')->store('ijazah', 'public');
            }
        }

        if (!empty($validated['tanggal_lahir'])) {
            $validated['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['tanggal_lahir'])->format('Y-m-d');
        }

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        $classes = SchoolClass::active()->ordered()->get();
        
        return view('admin.students.edit', compact('student', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'school_class_id' => 'required|exists:school_classes,id',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'nis' => 'nullable|string|max:50',
            'nisn' => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date_format:d/m/Y',
            'enrollment_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'is_active' => 'required|boolean',
            'status_lulus' => 'nullable|in:lulus,tidak_lulus',
            'ijazah_file' => 'nullable|mimes:pdf|max:500',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_active']) {
            $validated['status_lulus'] = null;
        }

        if (!$validated['is_active'] && isset($validated['status_lulus']) && $validated['status_lulus'] === 'lulus') {
            if ($request->hasFile('ijazah_file')) {
                // If old file exists, it could be deleted here, but skipping for simplicity as not requested
                $validated['ijazah_file'] = $request->file('ijazah_file')->store('ijazah', 'public');
            }
        } else {
            // Unset ijazah_file from validated to avoid overwriting with null if no new file uploaded?
            // Actually, if it's inactive but not 'lulus', maybe we should nullify it.
            if (!($request->hasFile('ijazah_file'))) {
                unset($validated['ijazah_file']);
            }
        }

        if (!empty($validated['tanggal_lahir'])) {
            $validated['tanggal_lahir'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['tanggal_lahir'])->format('Y-m-d');
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Show the import form.
     */
    public function import()
    {
        return view('admin.students.import');
    }

    /**
     * Store imported students.
     */
    public function storeImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt'
        ]);

        try {
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\StudentsImport, $request->file('file'));
            
            return response()->json([
                'success' => true,
                'message' => 'Data siswa berhasil diimpor.'
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            foreach ($failures as $failure) {
                $errors[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            // Check if it's the ZipArchive error
            if (strpos($e->getMessage(), 'ZipArchive') !== false) {
                 return response()->json([
                    'success' => false,
                    'message' => 'Server Error: Ekstensi Zip PHP tidak aktif. Silakan gunakan format file CSV.'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate(Request $request)
    {
        $classes = SchoolClass::active()->pluck('name')->toArray();
        $classExample = !empty($classes) ? $classes[0] : '7A';
        
        $data = [
            ['nama_siswa', 'nis', 'nisn', 'tanggal_lahir', 'jenis_kelamin', 'kelas', 'tahun_masuk'], // Header
            ['Budi Santoso', '12345', '0012345678', '17/08/2005', 'L', $classExample, date('Y')], // Example
            ['Siti Aminah', '12346', '0012345679', '21/04/2006', 'P', $classExample, date('Y')], // Example
        ];

        // Ensure we force CSV if requested or as fallback
        $format = $request->get('format', 'xlsx');
        $fileName = 'template_siswa.' . $format;
        
        if ($format === 'csv') {
             return \Maatwebsite\Excel\Facades\Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
                protected $data;
                public function __construct(array $data) { $this->data = $data; }
                public function array(): array { return $this->data; }
            }, $fileName, \Maatwebsite\Excel\Excel::CSV);
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;
            public function __construct(array $data) { $this->data = $data; }
            public function array(): array { return $this->data; }
        }, $fileName);
    }

    /**
     * Bulk update student status.
     */
    public function bulkStatusUpdate(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya administrator yang dapat mengakses fitur ini.'
            ], 403);
        }

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'status' => 'required|in:active,inactive'
        ]);

        $status = $request->status === 'active';
        
        Student::whereIn('id', $request->student_ids)->update([
            'is_active' => $status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status siswa berhasil diperbarui.'
        ]);
    }
}
