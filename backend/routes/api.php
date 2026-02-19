<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\ResultController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\AnnouncementController;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);
    Route::put('/auth/password', [AuthController::class, 'updatePassword']);

    // Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'admin']);
    Route::get('/dashboard/teacher', [DashboardController::class, 'teacher']);
    Route::get('/dashboard/student', [DashboardController::class, 'student']);
    Route::get('/dashboard/parent', [DashboardController::class, 'parent']);

    // Admin - Users
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index']);
        Route::post('/admin/users', [UserController::class, 'store']);
        Route::get('/admin/users/{user}', [UserController::class, 'show']);
        Route::put('/admin/users/{user}', [UserController::class, 'update']);
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy']);
        Route::post('/admin/users/{user}/assign-role', [UserController::class, 'assignRole']);
    });

    // Academic Management
    Route::middleware('role:admin|teacher')->group(function () {
        // Sessions
        Route::get('/academic/sessions', [AcademicController::class, 'sessionsIndex']);
        Route::post('/academic/sessions', [AcademicController::class, 'sessionsStore']);
        Route::get('/academic/sessions/{session}', [AcademicController::class, 'sessionsShow']);
        Route::put('/academic/sessions/{session}', [AcademicController::class, 'sessionsUpdate']);
        Route::delete('/academic/sessions/{session}', [AcademicController::class, 'sessionsDestroy']);

        // Terms
        Route::get('/academic/terms', [AcademicController::class, 'termsIndex']);
        Route::post('/academic/terms', [AcademicController::class, 'termsStore']);
        Route::get('/academic/terms/{term}', [AcademicController::class, 'termsShow']);
        Route::put('/academic/terms/{term}', [AcademicController::class, 'termsUpdate']);
        Route::delete('/academic/terms/{term}', [AcademicController::class, 'termsDestroy']);

        // Classes
        Route::get('/academic/classes', [AcademicController::class, 'classesIndex']);
        Route::post('/academic/classes', [AcademicController::class, 'classesStore']);
        Route::get('/academic/classes/{class}', [AcademicController::class, 'classesShow']);
        Route::put('/academic/classes/{class}', [AcademicController::class, 'classesUpdate']);
        Route::delete('/academic/classes/{class}', [AcademicController::class, 'classesDestroy']);

        // Subjects
        Route::get('/academic/subjects', [AcademicController::class, 'subjectsIndex']);
        Route::post('/academic/subjects', [AcademicController::class, 'subjectsStore']);
        Route::get('/academic/subjects/{subject}', [AcademicController::class, 'subjectsShow']);
        Route::put('/academic/subjects/{subject}', [AcademicController::class, 'subjectsUpdate']);
        Route::delete('/academic/subjects/{subject}', [AcademicController::class, 'subjectsDestroy']);

        // Current academic data
        Route::get('/academic/current', [AcademicController::class, 'currentAcademicData']);
    });

    // Assessments
    Route::get('/assessments', [AssessmentController::class, 'index']);
    Route::post('/assessments', [AssessmentController::class, 'store']);
    Route::get('/assessments/{assessment}', [AssessmentController::class, 'show']);
    Route::put('/assessments/{assessment}', [AssessmentController::class, 'update']);
    Route::delete('/assessments/{assessment}', [AssessmentController::class, 'destroy']);
    Route::post('/assessments/{assessment}/submit', [AssessmentController::class, 'submit']);
    Route::get('/assessments/{assessment}/submissions', [AssessmentController::class, 'submissions']);
    Route::post('/assessments/submissions/{submission}/grade', [AssessmentController::class, 'grade']);

    // Results
    Route::get('/results/student', [ResultController::class, 'studentResults']);
    Route::get('/results/student/{studentId}', [ResultController::class, 'studentByAssessment']);
    Route::get('/results/term/{termId}', [ResultController::class, 'termResults']);
    Route::get('/results/session/{sessionId}', [ResultController::class, 'sessionResults']);
    Route::get('/results/class/{classId}', [ResultController::class, 'classResults']);

    // Parent Routes
    Route::middleware('role:parent')->group(function () {
        Route::get('/parent/children', [DashboardController::class, 'parent']);
        Route::get('/parent/children/{studentId}/results', [ResultController::class, 'parentChildResults']);
        Route::get('/parent/children/{studentId}/results/term/{termId}', [ResultController::class, 'parentChildTermResults']);
        Route::get('/parent/children/{studentId}/results/session/{sessionId}', [ResultController::class, 'parentChildSessionResults']);
    });

    // Announcements
    Route::get('/announcements', [AnnouncementController::class, 'index']);
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show']);
    Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy']);
});

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok', 'message' => 'SchoolHub API is running']));
