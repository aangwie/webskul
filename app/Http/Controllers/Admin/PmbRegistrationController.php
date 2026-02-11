<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PmbRegistration;
use Illuminate\Http\Request;

class PmbRegistrationController extends Controller
{
    public function index()
    {
        $registrations = PmbRegistration::latest()->get();
        return view('admin.pmb-registrations.index', compact('registrations'));
    }

    public function show(PmbRegistration $pmbRegistration)
    {
        return view('admin.pmb-registrations.show', compact('pmbRegistration'));
    }

    public function updateStatus(Request $request, PmbRegistration $pmbRegistration)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $pmbRegistration->update($validated);

        return redirect()->back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function edit(PmbRegistration $pmbRegistration)
    {
        return view('admin.pmb-registrations.edit', compact('pmbRegistration'));
    }

    public function update(Request $request, PmbRegistration $pmbRegistration)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|string|max:20',
            'nik' => 'required|string|max:20',
            'birth_place' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'registration_type' => 'required|in:baru,pindahan',
            'mother_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'academic_year' => 'required|string|max:20',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $pmbRegistration->update($validated);

        return redirect()->route('admin.pmb-registrations.index')->with('success', 'Data pendaftaran berhasil diperbarui.');
    }

    public function destroy(PmbRegistration $pmbRegistration)
    {
        $pmbRegistration->delete();

        return redirect()->route('admin.pmb-registrations.index')->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
