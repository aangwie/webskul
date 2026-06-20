<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicServiceStandard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PublicServiceStandardController extends Controller
{
    public function index()
    {
        $standards = PublicServiceStandard::latest()->get();
        return view('admin.public_service_standards.index', compact('standards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120', // Max 5MB
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('public-service-standards', 'public');

            PublicServiceStandard::create([
                'title' => $file->getClientOriginalName(),
                'description' => $request->description,
                'file_path' => $path,
            ]);
        }

        return redirect()->route('admin.public-service-standards.index')->with('success', 'Dokumen SPP berhasil diunggah.');
    }

    public function destroy(PublicServiceStandard $publicServiceStandard)
    {
        if ($publicServiceStandard->file_path && Storage::disk('public')->exists($publicServiceStandard->file_path)) {
            Storage::disk('public')->delete($publicServiceStandard->file_path);
        }

        $publicServiceStandard->delete();
        return redirect()->route('admin.public-service-standards.index')->with('success', 'Dokumen SPP berhasil dihapus.');
    }
}