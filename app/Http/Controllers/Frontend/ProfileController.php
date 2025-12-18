<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use App\Models\Teacher;

class ProfileController extends Controller
{
    public function school()
    {
        $school = SchoolProfile::first();
        return view('pages.school-profile', compact('school'));
    }

    public function teachers()
    {
        $teachers = Teacher::active()->ordered()->get();
        
        foreach ($teachers as $teacher) {
            if ($teacher->photo && file_exists(storage_path('app/public/' . $teacher->photo))) {
                $path = storage_path('app/public/' . $teacher->photo);
                $data = file_get_contents($path);
                $teacher->photo = base64_encode($data);
            }
        }
        
        $school = SchoolProfile::first();
        return view('pages.teacher-profile', compact('teachers', 'school'));
    }
}
