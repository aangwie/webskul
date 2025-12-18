<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Information;
use App\Models\SchoolProfile;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => Teacher::count(),
            'activities' => Activity::count(),
            'informations' => Information::count(),
            'published_activities' => Activity::published()->count(),
        ];

        $latestActivities = Activity::latest()->take(5)->get();
        $school = SchoolProfile::first();

        return view('admin.dashboard', compact('stats', 'latestActivities', 'school'));
    }
}
