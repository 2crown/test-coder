<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $announcements = Announcement::with(['author', 'classModel'])
            ->when($request->class_id, function ($q, $id) { $q->where('class_id', $id); })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($announcements);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $validated['author_id'] = $request->user()->id;

        $announcement = Announcement::create($validated);

        return response()->json($announcement->load(['author', 'classModel']), 201);
    }

    public function show(Announcement $announcement)
    {
        return response()->json($announcement->load(['author', 'classModel']));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $announcement->update($validated);

        return response()->json($announcement->load(['author', 'classModel']));
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return response()->json(['message' => 'Announcement deleted']);
    }
}
