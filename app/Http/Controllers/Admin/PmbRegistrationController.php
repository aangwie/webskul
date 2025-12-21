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
}
