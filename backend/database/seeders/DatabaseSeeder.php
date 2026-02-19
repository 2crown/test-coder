<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher', 'guard_name' => 'web']);
        $studentRole = Role::firstOrCreate(['name' => 'student', 'guard_name' => 'web']);
        $parentRole = Role::firstOrCreate(['name' => 'parent', 'guard_name' => 'web']);

        // Create admin user
        $admin = \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@schoolhub.com',
            'password' => Hash::make('password123'),
            'phone' => '+2348000000000',
            'address' => 'School Administration',
        ]);
        $admin->assignRole('admin');

        // Create academic session
        $session = \App\Models\AcademicSession::create([
            'name' => '2025/2026',
            'start_date' => '2024-09-01',
            'end_date' => '2025-08-31',
            'is_current' => true,
        ]);

        // Create terms
        $firstTerm = \App\Models\Term::create([
            'name' => 'First Term',
            'academic_session_id' => $session->id,
            'start_date' => '2025-09-01',
            'end_date' => '2025-12-31',
            'is_current' => true,
        ]);

        \App\Models\Term::create([
            'name' => 'Second Term',
            'academic_session_id' => $session->id,
            'start_date' => '2026-01-01',
            'end_date' => '2026-04-30',
            'is_current' => false,
        ]);

        \App\Models\Term::create([
            'name' => 'Third Term',
            'academic_session_id' => $session->id,
            'start_date' => '2026-05-01',
            'end_date' => '2026-08-31',
            'is_current' => false,
        ]);

        // Create classes
        $jss1 = \App\Models\ClassModel::create([
            'name' => 'JSS 1',
            'level' => 'JSS1',
            'academic_year_id' => $session->id,
        ]);

        $ss1 = \App\Models\ClassModel::create([
            'name' => 'SS 1',
            'level' => 'SS1',
            'academic_year_id' => $session->id,
        ]);

        // Create teacher user and profile
        $teacher = \App\Models\User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@schoolhub.com',
            'password' => Hash::make('password123'),
            'phone' => '+2348000000001',
        ]);
        $teacher->assignRole('teacher');
        $teacherProfile = $teacher->teacher()->create([
            'employee_id' => 'TCH/001',
            'specialty' => 'Mathematics',
        ]);

        // Create subjects
        $mathSubject = \App\Models\Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH',
            'class_id' => $jss1->id,
            'teacher_id' => $teacherProfile->id,
        ]);

        \App\Models\Subject::create([
            'name' => 'English Language',
            'code' => 'ENG',
            'class_id' => $jss1->id,
            'teacher_id' => $teacherProfile->id,
        ]);

        // Create student user and profile
        $student = \App\Models\User::create([
            'name' => 'Alice Student',
            'email' => 'student@schoolhub.com',
            'password' => Hash::make('password123'),
        ]);
        $student->assignRole('student');
        $studentProfile = $student->student()->create([
            'admission_number' => 'STU/001',
            'class_id' => $jss1->id,
            'gender' => 'female',
            'date_of_birth' => '2010-05-15',
        ]);

        // Link student to subjects
        $studentProfile->subjects()->attach([$mathSubject->id]);

        // Create parent user and profile
        $parent = \App\Models\User::create([
            'name' => 'Mr. Parent',
            'email' => 'parent@schoolhub.com',
            'password' => Hash::make('password123'),
            'phone' => '+2348000000002',
        ]);
        $parent->assignRole('parent');
        $parentProfile = $parent->parent()->create([
            'occupation' => 'Business',
            'workplace' => 'Self Employed',
        ]);

        // Link parent to student
        $parentProfile->students()->attach([$studentProfile->id]);

        // Create sample assessment
        $assessment = \App\Models\Assessment::create([
            'title' => 'First Term Mathematics Test',
            'type' => 'test',
            'subject_id' => $mathSubject->id,
            'class_id' => $jss1->id,
            'term_id' => $firstTerm->id,
            'total_marks' => 50,
            'due_date' => '2024-12-15 12:00:00',
            'description' => 'This test covers all topics from week 1 to week 12.',
            'created_by' => $admin->id,
        ]);

        // Create sample result
        \App\Models\Result::create([
            'student_id' => $studentProfile->id,
            'subject_id' => $mathSubject->id,
            'assessment_id' => $assessment->id,
            'term_id' => $firstTerm->id,
            'marks' => 42,
            'grade' => 'A',
        ]);

        // Create sample announcement
        \App\Models\Announcement::create([
            'title' => 'Welcome to SchoolHub',
            'content' => 'Welcome to the new School Management System. Please explore all features.',
            'author_id' => $admin->id,
            'class_id' => null,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Default login credentials:');
        $this->command->info('Admin: admin@schoolhub.com / password123');
        $this->command->info('Teacher: teacher@schoolhub.com / password123');
        $this->command->info('Student: student@schoolhub.com / password123');
        $this->command->info('Parent: parent@schoolhub.com / password123');
    }
}
