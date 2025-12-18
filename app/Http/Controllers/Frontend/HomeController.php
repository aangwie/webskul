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
        $school = SchoolProfile::first();
        $latestActivities = Activity::published()->latest('published_at')->take(3)->get();
        $importantInfo = Information::active()->important()->latest()->take(5)->get();
        $featuredTeachers = Teacher::active()->ordered()->take(4)->get();

        return view('pages.home', compact('school', 'latestActivities', 'importantInfo', 'featuredTeachers'));
    }
}
