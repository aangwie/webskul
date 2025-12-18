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
        $school = SchoolProfile::first();
        return view('pages.teacher-profile', compact('teachers', 'school'));
    }
}
