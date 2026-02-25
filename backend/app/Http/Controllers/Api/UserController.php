<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with(['roles', 'student', 'teacher', 'parent'])
            ->when($request->role, function ($query, $role) {
                $query->role($role);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(15);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'role' => 'required|in:admin,teacher,student,parent',
            'admission_number' => 'nullable|string|unique:students',
            'employee_id' => 'nullable|string|unique:teachers',
            'class_id' => 'nullable|exists:classes,id',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'occupation' => 'nullable|string',
            'workplace' => 'nullable|string',
            'specialty' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);

            $user->assignRole($validated['role']);

            switch ($validated['role']) {
                case 'student':
                    $user->student()->create([
                        'admission_number' => $validated['admission_number'] ?? 'STU/' . time(),
                        'class_id' => $validated['class_id'] ?? null,
                        'gender' => $validated['gender'] ?? null,
                        'date_of_birth' => $validated['date_of_birth'] ?? null,
                    ]);
                    break;
                case 'teacher':
                    $user->teacher()->create([
                        'employee_id' => $validated['employee_id'] ?? 'TCH/' . time(),
                        'specialty' => $validated['specialty'] ?? null,
                    ]);
                    break;
                case 'parent':
                    $user->parent()->create([
                        'occupation' => $validated['occupation'] ?? null,
                        'workplace' => $validated['workplace'] ?? null,
                    ]);
                    break;
            }
        });

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function show(User $user)
    {
        $user->load(['roles', 'student.classModel', 'teacher', 'parent']);
        return response()->json(new UserResource($user));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'class_id' => 'nullable|exists:classes,id',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'occupation' => 'nullable|string',
            'workplace' => 'nullable|string',
            'specialty' => 'nullable|string',
        ]);

        $user->update($validated);

        if ($user->student) {
            $user->student->update($validated);
        }
        if ($user->teacher) {
            $user->teacher->update($validated);
        }
        if ($user->parent) {
            $user->parent->update($validated);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => new UserResource($user->fresh(['roles', 'student', 'teacher', 'parent']))
        ]);
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    public function destroy(User $user)
    {
        if ($user->student) {
            $user->student->delete();
        }
        if ($user->teacher) {
            $user->teacher->delete();
        }
        if ($user->parent) {
            $user->parent->delete();
        }
        
        $user->delete();
        
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,teacher,student,parent'
        ]);

        $user->syncRoles([$request->role]);

        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => new UserResource($user->fresh(['roles']))
        ]);
    }
}
