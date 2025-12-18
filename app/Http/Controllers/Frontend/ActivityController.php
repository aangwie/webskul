<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\SchoolProfile;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::published()->latest('published_at')->paginate(9);
        $school = SchoolProfile::first();
        return view('pages.activities', compact('activities', 'school'));
    }

    public function show($slug)
    {
        $activity = Activity::where('slug', $slug)->published()->firstOrFail();
        $relatedActivities = Activity::published()
            ->where('id', '!=', $activity->id)
            ->where('category', $activity->category)
            ->latest('published_at')
            ->take(3)
            ->get();
        $school = SchoolProfile::first();
        return view('pages.activity-detail', compact('activity', 'relatedActivities', 'school'));
    }
}
