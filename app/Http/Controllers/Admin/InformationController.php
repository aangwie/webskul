<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Information;
use Illuminate\Http\Request;

class InformationController extends Controller
{
    public function index()
    {
        $informations = Information::latest()->paginate(10);
        return view('admin.information.index', compact('informations'));
    }

    public function create()
    {
        return view('admin.information.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_important' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_important'] = $request->has('is_important');
        $validated['is_active'] = $request->has('is_active');

        Information::create($validated);

        return redirect()->route('admin.information.index')
            ->with('success', 'Informasi berhasil ditambahkan!');
    }

    public function edit(Information $information)
    {
        return view('admin.information.edit', compact('information'));
    }

    public function update(Request $request, Information $information)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'is_important' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_important'] = $request->has('is_important');
        $validated['is_active'] = $request->has('is_active');

        $information->update($validated);

        return redirect()->route('admin.information.index')
            ->with('success', 'Informasi berhasil diperbarui!');
    }

    public function destroy(Information $information)
    {
        $information->delete();

        return redirect()->route('admin.information.index')
            ->with('success', 'Informasi berhasil dihapus!');
    }
}
