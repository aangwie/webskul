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
            'tanggal_lahir' => 'required|date_format:d/m/Y'
        ]);

        $tanggal_lahir = \Carbon\Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir)->format('Y-m-d');

        $student = Student::where('nisn', $request->nisn)
                          ->where('tanggal_lahir', $tanggal_lahir)
                          ->first();

        return view('pages.ijazah', compact('student'));
    }
}
