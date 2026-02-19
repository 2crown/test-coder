<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Result;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $assessments = Assessment::with(['subject', 'classModel', 'term', 'creator'])
            ->when($request->class_id, function ($q, $id) { $q->where('class_id', $id); })
            ->when($request->subject_id, function ($q, $id) { $q->where('subject_id', $id); })
            ->when($request->type, function ($q, $type) { $q->where('type', $type); })
            ->when($request->term_id, function ($q, $id) { $q->where('term_id', $id); })
            ->when($request->created_by, function ($q, $id) { $q->where('created_by', $id); })
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return response()->json($assessments);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:assignment,test,exam',
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'nullable|exists:terms,id',
            'total_marks' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = $user->id;

        $assessment = Assessment::create($validated);

        return response()->json($assessment->load(['subject', 'classModel']), 201);
    }

    public function show(Assessment $assessment)
    {
        return response()->json($assessment->load(['subject', 'classModel', 'term', 'creator', 'submissions.student']));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:assignment,test,exam',
            'subject_id' => 'sometimes|exists:subjects,id',
            'class_id' => 'sometimes|exists:classes,id',
            'term_id' => 'nullable|exists:terms,id',
            'total_marks' => 'sometimes|integer|min:1',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $assessment->update($validated);

        return response()->json($assessment->load(['subject', 'classModel']));
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();
        return response()->json(['message' => 'Assessment deleted']);
    }

    public function submit(Request $request, Assessment $assessment)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240',
        ]);

        $student = $user->student;
        if (!$student) {
            return response()->json(['message' => 'User is not a student'], 403);
        }

        $submission = AssessmentSubmission::updateOrCreate(
            ['assessment_id' => $assessment->id, 'student_id' => $student->id],
            [
                'content' => $request->input('content'),
                'submitted_at' => now(),
            ]
        );

        return response()->json($submission->load('student'), 201);
    }

    public function submissions(Assessment $assessment)
    {
        $submissions = $assessment->submissions()->with('student.user')->get();
        return response()->json($submissions);
    }

    public function grade(Request $request, AssessmentSubmission $submission)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'marks' => 'required|numeric|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'marks' => $request->marks,
            'feedback' => $request->feedback,
            'graded_by' => $user->id,
        ]);

        // Create result entry
        $assessment = $submission->assessment;
        if (!$assessment) {
            return response()->json(['message' => 'Assessment not found'], 404);
        }

        Result::updateOrCreate(
            [
                'student_id' => $submission->student_id,
                'assessment_id' => $submission->assessment_id,
            ],
            [
                'subject_id' => $assessment->subject_id,
                'term_id' => $assessment->term_id,
                'marks' => $request->marks,
                'grade' => Result::calculateGrade($request->marks),
            ]
        );

        return response()->json($submission->load(['student', 'grader']));
    }
}
