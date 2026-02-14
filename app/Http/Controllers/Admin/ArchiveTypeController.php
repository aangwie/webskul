<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveType;
use Illuminate\Http\Request;

class ArchiveTypeController extends Controller
{
    public function index()
    {
        $types = ArchiveType::all();
        return view('admin.archive_types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ArchiveType::create($request->all());

        return redirect()->back()->with('success', 'Jenis Arsip berhasil ditambahkan');
    }

    public function update(Request $request, ArchiveType $archiveType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $archiveType->update($request->all());

        return redirect()->back()->with('success', 'Jenis Arsip berhasil diperbarui');
    }

    public function destroy(ArchiveType $archiveType)
    {
        $archiveType->delete();
        return redirect()->back()->with('success', 'Jenis Arsip berhasil dihapus');
    }
}
