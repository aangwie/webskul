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
        
        foreach ($activities as $activity) {
            if ($activity->image && file_exists(storage_path('app/public/' . $activity->image))) {
                $path = storage_path('app/public/' . $activity->image);
                $data = file_get_contents($path);
                $activity->image = base64_encode($data);
            }
        }

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
            
        if ($activity->image && file_exists(storage_path('app/public/' . $activity->image))) {
            $path = storage_path('app/public/' . $activity->image);
            $data = file_get_contents($path);
            $activity->image = base64_encode($data);
        }

        foreach ($relatedActivities as $related) {
            if ($related->image && file_exists(storage_path('app/public/' . $related->image))) {
                $path = storage_path('app/public/' . $related->image);
                $data = file_get_contents($path);
                $related->image = base64_encode($data);
            }
        }

        $school = SchoolProfile::first();
        return view('pages.activity-detail', compact('activity', 'relatedActivities', 'school'));
    }
}
