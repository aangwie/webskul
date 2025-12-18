<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Information;
use App\Models\SchoolProfile;

class InformationController extends Controller
{
    public function index()
    {
        $informations = Information::active()->latest()->paginate(10);
        $school = SchoolProfile::first();
        return view('pages.information', compact('informations', 'school'));
    }
}
