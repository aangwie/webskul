<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicComplaint;
use Illuminate\Http\Request;

class PublicComplaintController extends Controller
{
    public function index()
    {
        $complaints = PublicComplaint::orderBy('created_at', 'desc')->get();
        return view('admin.complaints.index', compact('complaints'));
    }

    public function respond(Request $request, PublicComplaint $publicComplaint)
    {
        $request->validate([
            'response' => 'required',
        ]);

        $publicComplaint->update([
            'response' => $request->response,
            'status' => 'responded',
        ]);

        return back()->with('success', 'Tanggapan berhasil dikirim!');
    }

    public function destroy(PublicComplaint $publicComplaint)
    {
        $publicComplaint->delete();
        return back()->with('success', 'Aduan berhasil dihapus!');
    }
}
