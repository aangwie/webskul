<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolFacility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolFacilityController extends Controller
{
    public function index()
    {
        $facilities = SchoolFacility::latest()->get();
        return view('admin.school_facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('admin.school_facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'description' => 'required|string',
        ]);

        $facility = new SchoolFacility();
        $facility->description = $request->description;

        if ($request->hasFile('image')) {
            $facility->image = $this->convertToWebP($request->file('image'));
        }

        $facility->save();

        return redirect()->route('admin.school-facilities.index')->with('success', 'Fasilitas berhasil ditambahkan');
    }

    public function edit(SchoolFacility $schoolFacility)
    {
        return view('admin.school_facilities.edit', compact('schoolFacility'));
    }

    public function update(Request $request, SchoolFacility $schoolFacility)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'description' => 'required|string',
        ]);

        $schoolFacility->description = $request->description;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($schoolFacility->image) {
                Storage::disk('public')->delete($schoolFacility->image);
            }
            $schoolFacility->image = $this->convertToWebP($request->file('image'));
        }

        $schoolFacility->save();

        return redirect()->route('admin.school-facilities.index')->with('success', 'Fasilitas berhasil diperbarui');
    }

    public function destroy(SchoolFacility $schoolFacility)
    {
        if ($schoolFacility->image) {
            Storage::disk('public')->delete($schoolFacility->image);
        }
        $schoolFacility->delete();

        return redirect()->route('admin.school-facilities.index')->with('success', 'Fasilitas terhapus');
    }

    protected function convertToWebP($file)
    {
        $filename = 'facility_' . time() . '_' . Str::random(5) . '.webp';
        $directory = storage_path('app/public/facilities');
        $path = $directory . '/' . $filename;

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $imageResource = null;

        if (in_array($extension, ['jpg', 'jpeg'])) {
            $imageResource = @imagecreatefromjpeg($file->getRealPath());
        } elseif ($extension == 'png') {
            $imageResource = @imagecreatefrompng($file->getRealPath());
            if ($imageResource) {
                imagepalettetotruecolor($imageResource);
                imagealphablending($imageResource, true);
                imagesavealpha($imageResource, true);
            }
        } elseif ($extension == 'gif') {
            $imageResource = @imagecreatefromgif($file->getRealPath());
        } elseif ($extension == 'webp') {
            $file->storeAs('facilities', $filename, 'public');
            return 'facilities/' . $filename;
        }

        if ($imageResource) {
            imagewebp($imageResource, $path, 100);
            imagedestroy($imageResource);
            return 'facilities/' . $filename;
        }

        // Fallback if GD fails
        $fallbackFilename = 'facility_' . time() . '_' . Str::random(5) . '.' . $extension;
        $file->storeAs('facilities', $fallbackFilename, 'public');
        return 'facilities/' . $fallbackFilename;
    }
}
