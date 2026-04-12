<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;

class IjazahController extends Controller
{
    public function index()
    {
        return view('pages.ijazah');
    }

    public function check(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'tanggal_lahir' => 'required|date'
        ]);

        $student = Student::where('nisn', $request->nisn)
                          ->where('tanggal_lahir', $request->tanggal_lahir)
                          ->first();

        return view('pages.ijazah', compact('student'));
    }
}
