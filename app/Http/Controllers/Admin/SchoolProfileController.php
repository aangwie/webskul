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
            'logo_ssn' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        if ($request->hasFile('logo_ssn')) {
            // Delete old logo ssn if it exists and is not base64
            if ($school->logo_ssn && !Str::startsWith($school->logo_ssn, 'data:')) {
                Storage::disk('public')->delete($school->logo_ssn);
            }

            $pathSsn = $request->file('logo_ssn')->store('school', 'public');
            $validated['logo_ssn'] = $pathSsn;
        }

        $school->fill($validated);
        $school->save();

        return redirect()->route('admin.school-profile.index')
            ->with('success', 'Profil sekolah berhasil diperbarui!');
    }

    public function deleteLogo(Request $request, $type)
    {
        $school = SchoolProfile::first();
        if (!$school) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Profil sekolah tidak ditemukan.'], 404);
            }
            return redirect()->back()->with('error', 'Profil sekolah tidak ditemukan.');
        }

        $field = $type === 'ssn' ? 'logo_ssn' : 'logo';

        if ($school->$field && !Str::startsWith($school->$field, 'data:')) {
            Storage::disk('public')->delete($school->$field);
        }

        $school->$field = null;
        $school->save();

        $label = $type === 'ssn' ? 'Logo SSN' : 'Logo Sekolah';
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "$label berhasil dihapus!"]);
        }
        return redirect()->back()->with('success', "$label berhasil dihapus!");
    }
}
