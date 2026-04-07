<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SchoolFacility;
use Illuminate\Http\Request;

class SchoolFacilityController extends Controller
{
    public function index()
    {
        $facilities = SchoolFacility::latest()->get();
        return view('frontend.facilities.index', compact('facilities'));
    }
}
