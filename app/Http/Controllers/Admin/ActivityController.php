<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::latest()->paginate(10);
        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.activities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:news,event',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published']) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('activities', 'public');
            $validated['image'] = $path;
        }

        Activity::create($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil ditambahkan!');
    }

    public function edit(Activity $activity)
    {
        return view('admin.activities.edit', compact('activity'));
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:news,event',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['is_published'] = $request->has('is_published');

        if ($validated['is_published'] && !$activity->published_at) {
            $validated['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            // Delete old image
            if ($activity->image && !Str::startsWith($activity->image, 'data:')) {
                Storage::disk('public')->delete($activity->image);
            }

            $path = $request->file('image')->store('activities', 'public');
            $validated['image'] = $path;
        }

        $activity->update($validated);

        return redirect()->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil diperbarui!');
    }

    public function destroy(Activity $activity)
    {
        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }
        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Kegiatan berhasil dihapus!');
    }
}
