<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Information;
use App\Models\SchoolProfile;
use App\Models\Teacher;

class HomeController extends Controller
{
    public function index()
    {
        $latestActivities = Activity::published()->latest('published_at')->take(3)->get();
        $importantInfo = Information::active()->important()->latest()->take(5)->get();
        $featuredTeachers = Teacher::active()->count();

        // Student Statistics
        $studentStats = [
            'total_classes' => \App\Models\SchoolClass::active()->count(),
            'total_students' => \App\Models\Student::active()->count(),
            'male_students' => \App\Models\Student::active()->male()->count(),
            'female_students' => \App\Models\Student::active()->female()->count(),
        ];

        // Class Breakdown
        $classStats = \App\Models\SchoolClass::active()
            ->withCount([
                'students as total' => function ($q) {
                    $q->where('is_active', true);
                },
                'students as male' => function ($q) {
                    $q->where('is_active', true)->where('gender', 'male');
                },
                'students as female' => function ($q) {
                    $q->where('is_active', true)->where('gender', 'female');
                }
            ])
            ->ordered()
            ->get();

        // Yearly Enrollment for Graph
        $enrollmentData = \App\Models\Student::selectRaw('enrollment_year, count(*) as total')
            ->groupBy('enrollment_year')
            ->orderBy('enrollment_year', 'asc')
            ->limit(5) // Last 5 years
            ->get();

        return view('pages.home', compact('latestActivities', 'importantInfo', 'featuredTeachers', 'studentStats', 'classStats', 'enrollmentData'));
    }
}
