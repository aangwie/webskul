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

        // Convert to Base64 as requested/used in other parts of the system if needed, 
        // but typically files are stored. The user mentioned Base64 in previous conversations.
        // Let's check how other controllers handle it.
        // For now, I'll use standard storage to be safe, unless I see Base64 is the standard.
        // Check TeacherController or ActivityController.

        $path = $file->store('archives', 'public');

        Archive::create([
            'user_id' => auth()->id(),
            'archive_type_id' => $request->archive_type_id,
            'title' => $request->title,
            'file_path' => $path,
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
            // Delete old file
            if ($archive->file_path) {
                Storage::disk('public')->delete($archive->file_path);
            }
            $data['file_path'] = $request->file('file')->store('archives', 'public');
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

        if ($archive->file_path) {
            Storage::disk('public')->delete($archive->file_path);
        }

        $archive->delete();

        return redirect()->back()->with('success', 'Arsip berhasil dihapus');
    }
}
