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
            'brand_subtitle' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'history' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'logo_ssn' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'maklumat_pelayanan_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:1024',
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

        if ($request->hasFile('maklumat_pelayanan_image')) {
            $file = $request->file('maklumat_pelayanan_image');
            // Convert to WebP and encode as base64
            $image = imagecreatefromstring(file_get_contents($file->getRealPath()));
            if ($image !== false) {
                ob_start();
                imagewebp($image, null, 80);
                $webpData = ob_get_clean();
                imagedestroy($image);
                $validated['maklumat_pelayanan_image'] = 'data:image/webp;base64,' . base64_encode($webpData);
            } else {
                // Fallback: store as original format base64 if GD can't read it
                $validated['maklumat_pelayanan_image'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
            }
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

        if ($type === 'maklumat') {
            $school->maklumat_pelayanan_image = null;
            $school->save();

            $label = 'Gambar Maklumat Pelayanan';
            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => "$label berhasil dihapus!"]);
            }
            return redirect()->back()->with('success', "$label berhasil dihapus!");
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
