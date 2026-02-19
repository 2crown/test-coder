<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicSession;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function studentResults(Request $request)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $results = Result::with(['subject', 'assessment', 'term'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json($results);
    }

    public function studentByAssessment(Request $request, $studentId)
    {
        $results = Result::with(['subject', 'assessment'])
            ->where('student_id', $studentId)
            ->get();

        return response()->json($results);
    }

    public function termResults(Request $request, $termId)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $results = Result::with(['subject', 'assessment'])
            ->where('student_id', $student->id)
            ->where('term_id', $termId)
            ->get();

        $totalMarks = $results->sum('marks');
        $average = $results->count() > 0 ? $results->avg('marks') : 0;

        return response()->json([
            'results' => $results,
            'total_marks' => $totalMarks,
            'average' => round($average, 2),
            'subjects_count' => $results->count(),
        ]);
    }

    public function sessionResults(Request $request, $sessionId)
    {
        $student = $request->user()->student;
        
        if (!$student) {
            return response()->json(['message' => 'Student profile not found'], 404);
        }

        $termIds = Term::where('academic_session_id', $sessionId)->pluck('id');

        $results = Result::with(['subject', 'assessment', 'term'])
            ->where('student_id', $student->id)
            ->whereIn('term_id', $termIds)
            ->get();

        $groupedByTerm = $results->groupBy('term_id');
        $termSummaries = [];

        foreach ($groupedByTerm as $termId => $termResults) {
            $term = Term::find($termId);
            $termSummaries[] = [
                'term' => $term,
                'results' => $termResults,
                'total_marks' => $termResults->sum('marks'),
                'average' => round($termResults->avg('marks'), 2),
            ];
        }

        $overallTotal = $results->sum('marks');
        $overallAverage = $results->count() > 0 ? $results->avg('marks') : 0;

        return response()->json([
            'term_summaries' => $termSummaries,
            'overall_total_marks' => $overallTotal,
            'overall_average' => round($overallAverage, 2),
            'subjects_count' => $results->count(),
        ]);
    }

    public function classResults(Request $request, $classId)
    {
        $results = Result::with(['student.user', 'subject'])
            ->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->when($request->subject_id, function ($q, $id) { $q->where('subject_id', $id); })
            ->when($request->term_id, function ($q, $id) { $q->where('term_id', $id); })
            ->get();

        return response()->json($results);
    }

    public function parentChildResults(Request $request, $studentId)
    {
        $results = Result::with(['subject', 'assessment', 'term'])
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        return response()->json($results);
    }

    public function parentChildTermResults(Request $request, $studentId, $termId)
    {
        $results = Result::with(['subject', 'assessment'])
            ->where('student_id', $studentId)
            ->where('term_id', $termId)
            ->get();

        $totalMarks = $results->sum('marks');
        $average = $results->count() > 0 ? $results->avg('marks') : 0;

        return response()->json([
            'results' => $results,
            'total_marks' => $totalMarks,
            'average' => round($average, 2),
            'subjects_count' => $results->count(),
        ]);
    }

    public function parentChildSessionResults(Request $request, $studentId, $sessionId)
    {
        $termIds = Term::where('academic_session_id', $sessionId)->pluck('id');

        $results = Result::with(['subject', 'assessment', 'term'])
            ->where('student_id', $studentId)
            ->whereIn('term_id', $termIds)
            ->get();

        $groupedByTerm = $results->groupBy('term_id');
        $termSummaries = [];

        foreach ($groupedByTerm as $termId => $termResults) {
            $term = Term::find($termId);
            $termSummaries[] = [
                'term' => $term,
                'results' => $termResults,
                'total_marks' => $termResults->sum('marks'),
                'average' => round($termResults->avg('marks'), 2),
            ];
        }

        $overallTotal = $results->sum('marks');
        $overallAverage = $results->count() > 0 ? $results->avg('marks') : 0;

        return response()->json([
            'term_summaries' => $termSummaries,
            'overall_total_marks' => $overallTotal,
            'overall_average' => round($overallAverage, 2),
            'subjects_count' => $results->count(),
        ]);
    }
}
