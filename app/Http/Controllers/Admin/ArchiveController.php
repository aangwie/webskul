<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Archive;
use App\Models\ArchiveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            $archives = Archive::with(['user', 'archiveType'])->latest()->get();
        } else {
            $archives = Archive::with(['archiveType'])->where('user_id', $user->id)->latest()->get();
        }

        $types = ArchiveType::all();
        return view('admin.archives.index', compact('archives', 'types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'archive_type_id' => 'required|exists:archive_types,id',
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:500',
            'description' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $base64 = 'data:' . $file->getClientMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        Archive::create([
            'user_id' => auth()->id(),
            'archive_type_id' => $request->archive_type_id,
            'title' => $request->title,
            'file_path' => $base64,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Arsip berhasil diunggah');
    }

    public function update(Request $request, Archive $archive)
    {
        // Security check
        if (!auth()->user()->isAdmin() && $archive->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'archive_type_id' => 'required|exists:archive_types,id',
            'title' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:500',
            'description' => 'nullable|string',
        ]);

        $data = [
            'archive_type_id' => $request->archive_type_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $data['file_path'] = 'data:' . $file->getClientMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $archive->update($data);

        return redirect()->back()->with('success', 'Arsip berhasil diperbarui');
    }

    public function destroy(Archive $archive)
    {
        // Security check
        if (!auth()->user()->isAdmin() && $archive->user_id !== auth()->id()) {
            abort(403);
        }

        $archive->delete();

        return redirect()->back()->with('success', 'Arsip berhasil dihapus');
    }
}
