<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Result;
use App\Models\Student;
use App\Models\AcademicSession;
use App\Models\Term;
use App\Models\Announcement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        $totalStudents = Student::count();
        $totalTeachers = \App\Models\Teacher::count();
        $totalParents = \App\Models\ParentModel::count();
        $totalClasses = \App\Models\ClassModel::count();
        $totalSubjects = \App\Models\Subject::count();
        
        $currentSession = AcademicSession::where('is_current', true)->first();
        $currentTerm = Term::where('is_current', true)->first();

        $recentAssessments = Assessment::with(['subject', 'classModel'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentAnnouncements = Announcement::with('author')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_students' => $totalStudents,
                'total_teachers' => $totalTeachers,
                'total_parents' => $totalParents,
                'total_classes' => $totalClasses,
                'total_subjects' => $totalSubjects,
            ],
            'current_session' => $currentSession,
            'current_term' => $currentTerm,
            'recent_assessments' => $recentAssessments,
            'recent_announcements' => $recentAnnouncements,
        ]);
    }

    public function teacher(Request $request)
    {
        $user = $request->user();
        $teacher = $user->teacher;

        $myClasses = \App\Models\ClassModel::whereHas('subjects', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();

        $mySubjects = \App\Models\Subject::where('teacher_id', $teacher->id)->get();

        $pendingGrading = Assessment::whereIn('subject_id', $mySubjects->pluck('id'))
            ->whereHas('submissions', function ($q) {
                $q->whereNotNull('submitted_at')->whereNull('marks');
            })
            ->with(['subject', 'classModel'])
            ->get();

        $myAssessments = Assessment::whereIn('subject_id', $mySubjects->pluck('id'))
            ->with(['subject', 'classModel'])
            ->orderBy('due_date', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'teacher' => $teacher,
            'my_classes' => $myClasses,
            'my_subjects' => $mySubjects,
            'pending_grading' => $pendingGrading,
            'my_assessments' => $myAssessments,
        ]);
    }

    public function student(Request $request)
    {
        $user = $request->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $student->load(['classModel', 'subjects']);

        $upcomingAssessments = Assessment::where('class_id', $student->class_id)
            ->where('due_date', '>=', now())
            ->with(['subject', 'classModel'])
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        $recentResults = Result::where('student_id', $student->id)
            ->with(['subject', 'assessment'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $currentTerm = Term::where('is_current', true)->first();
        $currentSession = AcademicSession::where('is_current', true)->first();

        return response()->json([
            'student' => $student,
            'upcoming_assessments' => $upcomingAssessments,
            'recent_results' => $recentResults,
            'current_term' => $currentTerm,
            'current_session' => $currentSession,
        ]);
    }

    public function parent(Request $request)
    {
        $user = $request->user();
        $parent = $user->parent;

        if (!$parent) {
            return response()->json(['message' => 'Parent profile not found'], 404);
        }

        $children = $parent->students()->with(['user', 'classModel'])->get();

        $childrenData = [];
        foreach ($children as $child) {
            $recentResults = Result::where('student_id', $child->id)
                ->with(['subject', 'assessment'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $childrenData[] = [
                'student' => $child->load('user'),
                'recent_results' => $recentResults,
            ];
        }

        return response()->json([
            'parent' => $parent,
            'children' => $childrenData,
        ]);
    }
}
