<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PublicServiceStandard;
use Illuminate\Http\Request;

class PublicServiceStandardController extends Controller
{
    public function index()
    {
        $standards = PublicServiceStandard::latest()->get();
        return view('frontend.public_service_standards.index', compact('standards'));
    }

    public function showPdf(PublicServiceStandard $publicServiceStandard)
    {
        $path = storage_path('app/public/' . $publicServiceStandard->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }

    public function downloadPdf(PublicServiceStandard $publicServiceStandard)
    {
        $path = storage_path('app/public/' . $publicServiceStandard->file_path);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }
}