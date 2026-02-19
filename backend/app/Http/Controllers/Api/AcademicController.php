<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AcademicSessionResource;
use App\Http\Resources\TermResource;
use App\Http\Resources\ClassResource;
use App\Http\Resources\SubjectResource;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\ClassModel;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    // Academic Sessions
    public function sessionsIndex(Request $request)
    {
        $sessions = AcademicSession::with('terms')
            ->when($request->current, fn($q) => $q->where('is_current', true))
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => AcademicSessionResource::collection($sessions),
            'meta' => [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'per_page' => $sessions->perPage(),
                'total' => $sessions->total()
            ]
        ]);
    }

    public function sessionsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        if ($validated['is_current'] ?? false) {
            AcademicSession::where('is_current', true)->update(['is_current' => false]);
        }

        $session = AcademicSession::create($validated);
        
        return response()->json([
            'message' => 'Academic session created successfully',
            'data' => new AcademicSessionResource($session->load('terms'))
        ], 201);
    }

    public function sessionsShow(AcademicSession $session)
    {
        return response()->json([
            'data' => new AcademicSessionResource($session->load('terms'))
        ]);
    }

    public function sessionsUpdate(Request $request, AcademicSession $session)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        if (($validated['is_current'] ?? false) && !$session->is_current) {
            AcademicSession::where('is_current', true)->where('id', '!=', $session->id)->update(['is_current' => false]);
        }

        $session->update($validated);

        return response()->json([
            'message' => 'Academic session updated successfully',
            'data' => new AcademicSessionResource($session->fresh('terms'))
        ]);
    }

    public function sessionsDestroy(AcademicSession $session)
    {
        if ($session->classes()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete session with existing classes'
            ], 422);
        }
        
        $session->delete();
        
        return response()->json([
            'message' => 'Academic session deleted successfully'
        ]);
    }

    // Terms
    public function termsIndex(Request $request)
    {
        $terms = Term::with('academicSession')
            ->when($request->session_id, fn($q, $id) => $q->where('academic_session_id', $id))
            ->when($request->current, fn($q) => $q->where('is_current', true))
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return response()->json([
            'data' => TermResource::collection($terms),
            'meta' => [
                'current_page' => $terms->currentPage(),
                'last_page' => $terms->lastPage(),
                'per_page' => $terms->perPage(),
                'total' => $terms->total()
            ]
        ]);
    }

    public function termsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        if ($validated['is_current'] ?? false) {
            Term::where('is_current', true)->update(['is_current' => false]);
        }

        $term = Term::create($validated);
        
        return response()->json([
            'message' => 'Term created successfully',
            'data' => new TermResource($term->load('academicSession'))
        ], 201);
    }

    public function termsShow(Term $term)
    {
        return response()->json([
            'data' => new TermResource($term->load('academicSession'))
        ]);
    }

    public function termsUpdate(Request $request, Term $term)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'academic_session_id' => 'sometimes|exists:academic_sessions,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'is_current' => 'sometimes|boolean',
        ]);

        $term->update($validated);

        return response()->json([
            'message' => 'Term updated successfully',
            'data' => new TermResource($term->fresh('academicSession'))
        ]);
    }

    public function termsDestroy(Term $term)
    {
        if ($term->assessments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete term with existing assessments'
            ], 422);
        }
        
        $term->delete();
        
        return response()->json([
            'message' => 'Term deleted successfully'
        ]);
    }

    // Classes
    public function classesIndex(Request $request)
    {
        $classes = ClassModel::with('academicYear')
            ->withCount('students', 'subjects')
            ->when($request->academic_year_id, fn($q, $id) => $q->where('academic_year_id', $id))
            ->orderBy('name')
            ->paginate(15);

        return response()->json([
            'data' => ClassResource::collection($classes),
            'meta' => [
                'current_page' => $classes->currentPage(),
                'last_page' => $classes->lastPage(),
                'per_page' => $classes->perPage(),
                'total' => $classes->total()
            ]
        ]);
    }

    public function classesStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $class = ClassModel::create($validated);
        
        return response()->json([
            'message' => 'Class created successfully',
            'data' => new ClassResource($class->load('academicYear'))
        ], 201);
    }

    public function classesShow(ClassModel $class)
    {
        return response()->json([
            'data' => new ClassResource($class->load(['academicYear', 'subjects.teacher', 'students']))
        ]);
    }

    public function classesUpdate(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'level' => 'sometimes|string|max:50',
            'academic_year_id' => 'nullable|exists:academic_sessions,id',
        ]);

        $class->update($validated);

        return response()->json([
            'message' => 'Class updated successfully',
            'data' => new ClassResource($class->fresh('academicYear'))
        ]);
    }

    public function classesDestroy(ClassModel $class)
    {
        if ($class->students()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete class with enrolled students'
            ], 422);
        }
        
        $class->delete();
        
        return response()->json([
            'message' => 'Class deleted successfully'
        ]);
    }

    // Subjects
    public function subjectsIndex(Request $request)
    {
        $subjects = Subject::with(['classModel', 'teacher.user'])
            ->withCount('students')
            ->when($request->class_id, fn($q, $id) => $q->where('class_id', $id))
            ->when($request->teacher_id, fn($q, $id) => $q->where('teacher_id', $id))
            ->orderBy('name')
            ->paginate(15);

        return response()->json([
            'data' => SubjectResource::collection($subjects),
            'meta' => [
                'current_page' => $subjects->currentPage(),
                'last_page' => $subjects->lastPage(),
                'per_page' => $subjects->perPage(),
                'total' => $subjects->total()
            ]
        ]);
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects',
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $subject = Subject::create($validated);
        
        return response()->json([
            'message' => 'Subject created successfully',
            'data' => new SubjectResource($subject->load(['classModel', 'teacher']))
        ], 201);
    }

    public function subjectsShow(Subject $subject)
    {
        return response()->json([
            'data' => new SubjectResource($subject->load(['classModel', 'teacher.user', 'students']))
        ]);
    }

    public function subjectsUpdate(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:20|unique:subjects,code,' . $subject->id,
            'class_id' => 'sometimes|exists:classes,id',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $subject->update($validated);

        return response()->json([
            'message' => 'Subject updated successfully',
            'data' => new SubjectResource($subject->fresh(['classModel', 'teacher']))
        ]);
    }

    public function subjectsDestroy(Subject $subject)
    {
        if ($subject->assessments()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete subject with existing assessments'
            ], 422);
        }
        
        $subject->delete();
        
        return response()->json([
            'message' => 'Subject deleted successfully'
        ]);
    }

    // Get current academic data
    public function currentAcademicData()
    {
        $currentSession = AcademicSession::where('is_current', true)
            ->with('terms', fn($q) => $q->where('is_current', true))
            ->first();

        $classes = ClassModel::with('academicYear')
            ->withCount('students')
            ->get();

        return response()->json([
            'current_session' => $currentSession ? new AcademicSessionResource($currentSession) : null,
            'classes' => ClassResource::collection($classes)
        ]);
    }
}
