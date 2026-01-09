<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SchoolProfileController extends Controller
{
    public function index()
    {
        $school = SchoolProfile::first();
        return view('admin.school-profile.index', compact('school'));
    }

    public function edit()
    {
        $school = SchoolProfile::first();
        if (!$school) {
            $school = SchoolProfile::create(['name' => 'SMP Negeri 6 Sudimoro']);
        }
        return view('admin.school-profile.edit', compact('school'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'history' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $school = SchoolProfile::first();
        if (!$school) {
            $school = new SchoolProfile();
        }

        if ($request->hasFile('logo')) {
            // Delete old logo if it exists and is not base64
            if ($school->logo && !Str::startsWith($school->logo, 'data:')) {
                Storage::disk('public')->delete($school->logo);
            }

            $path = $request->file('logo')->store('school', 'public');
            $validated['logo'] = $path;
        }

        $school->fill($validated);
        $school->save();

        return redirect()->route('admin.school-profile.index')
            ->with('success', 'Profil sekolah berhasil diperbarui!');
    }
}
