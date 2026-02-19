# School Management Application Specification

## 1. Project Overview

**Project Name:** SchoolHub - School Management System

**Project Type:** Full-stack Web Application (Laravel API + React Frontend)

**Core Functionality:** A comprehensive school management portal enabling students to take assessments and view results, teachers to manage academic content, parents to monitor their children's progress, and administrators to manage all school operations.

**Target Users:** Students, Teachers, Parents, Administrators

---

## 2. Technology Stack

### Backend
- **Framework:** Laravel 10.x
- **Authentication:** Laravel Sanctum
- **Authorization:** Spatie Permission (Role-based Access Control)
- **Database:** MySQL/SQLite

### Frontend
- **Framework:** React 18 with Vite
- **Routing:** React Router DOM v6
- **State Management:** Redux Toolkit with RTK Query
- **Styling:** Tailwind CSS + shadcn/ui
- **HTTP Client:** Axios

---

## 3. User Roles & Permissions

### Admin
- Full system access
- Manage all users (CRUD)
- Manage academic sessions and terms
- Manage classes/grades
- Manage subjects
- Manage teacher assignments
- View all reports and analytics
- System settings

### Teacher
- Manage assigned classes/subjects
- Create and upload assignments, tests, examinations
- Grade assignments, tests, examinations
- View class performance
- Manage class announcements

### Student
- View enrolled classes and subjects
- Take assignments, tests, examinations
- View personal results (test, assignment, exam)
- View termly and sessional results
- View announcements

### Parent
- Link to children (students)
- View children's assignments, tests, examinations
- View children's results
- View children's academic progress
- View announcements

---

## 4. Database Schema

### Users Table
- id, name, email, password, phone, address, avatar
- role (admin, teacher, student, parent)
- created_at, updated_at

### Students (extends users)
- id, user_id, admission_number, class_id, parent_id
- date_of_birth, gender

### Parents (extends users)
- id, user_id, occupation, workplace

### Teachers (extends users)
- id, user_id, employee_id, specialty

### Classes/Grades Table
- id, name, level, academic_year_id

### Academic Sessions Table
- id, name, start_date, end_date, is_current

### Terms Table
- id, name, academic_session_id, start_date, end_date, is_current

### Subjects Table
- id, name, code, class_id, teacher_id

### Student Subjects (pivot)
- student_id, subject_id

### Assessments Table
- id, title, type (assignment/test/exam), subject_id, class_id
- term_id, total_marks, due_date, description, created_by

### Assessment Submissions Table
- id, assessment_id, student_id, file_path, submitted_at, marks, feedback, graded_by

### Results Table
- id, student_id, subject_id, assessment_id, term_id, marks, grade

### Announcements Table
- id, title, content, author_id, class_id (optional), created_at

### Student-Parent Links Table
- student_id, parent_id

---

## 5. API Endpoints

### Authentication
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout
- GET /api/auth/user
- PUT /api/auth/profile

### Admin Management
- GET/POST /api/admin/users
- GET/PUT/DELETE /api/admin/users/{id}
- GET/POST /api/admin/academic-sessions
- GET/POST /api/admin/classes
- GET/POST /api/admin/subjects
- GET/POST /api/admin/teachers/assign

### Academic
- GET/POST /api/academic/sessions
- GET/POST /api/academic/terms
- GET/POST /api/academic/classes
- GET/POST /api/academic/subjects

### Assessments
- GET/POST /api/assessments
- GET/PUT/DELETE /api/assessments/{id}
- POST /api/assessments/{id}/submit
- POST /api/assessments/{id}/grade
- GET /api/assessments/{id}/submissions

### Results
- GET /api/results/student/{id}
- GET /api/results/subject/{subjectId}/class/{classId}
- GET /api/results/term/{termId}/student/{studentId}
- GET /api/results/session/{sessionId}/student/{studentId}

### Dashboard
- GET /api/dashboard/student
- GET /api/dashboard/teacher
- GET /api/dashboard/parent
- GET /api/dashboard/admin

---

## 6. Frontend Pages & Components

### Common Components
- Navbar, Sidebar, Layout
- DataTable, Form inputs (shadcn)
- Modal, Toast notifications
- ProtectedRoute (role-based)

### Pages

#### Authentication
- Login Page
- Register Page (role-based registration - admin only can create users)

#### Dashboard (Role-specific)
- Admin Dashboard: Stats cards, recent activities, quick actions
- Teacher Dashboard: My classes, pending grading, announcements
- Student Dashboard: Upcoming assessments, recent results, announcements
- Parent Dashboard: Children progress, upcoming events

#### Admin Pages
- User Management (CRUD)
- Academic Session Management
- Class Management
- Subject Management
- Teacher Assignment
- Reports & Analytics

#### Teacher Pages
- My Classes
- Assessment Management (Create/Edit/Delete assignments, tests, exams)
- Grade Submissions
- Class Results
- Announcements

#### Student Pages
- My Classes & Subjects
- Available Assessments
- My Submissions
- My Results (Test/Assignment/Exam)
- Termly Results
- Sessional Results

#### Parent Pages
- Linked Children
- Child's Assessments
- Child's Results
- Academic Progress

---

## 7. UI/UX Design

### Color Palette
- Primary: #3B82F6 (Blue)
- Secondary: #10B981 (Green)
- Accent: #F59E0B (Amber)
- Background: #F8FAFC
- Card Background: #FFFFFF
- Text Primary: #1E293B
- Text Secondary: #64748B
- Error: #EF4444
- Success: #22C55E

### Layout
- Sidebar navigation (collapsible)
- Top navbar with user menu
- Responsive design (mobile-friendly)
- Card-based content display

### Components
- shadcn/ui components: Button, Card, Input, Select, Table, Dialog, Dropdown, Avatar, Badge, Tabs
- Tailwind utility classes for spacing, typography

---

## 8. Acceptance Criteria

### Authentication
- [ ] Users can register and login
- [ ] JWT tokens are used for API authentication
- [ ] Role-based route protection works

### Admin
- [ ] Can create, edit, delete users
- [ ] Can manage academic sessions and terms
- [ ] Can manage classes and subjects
- [ ] Can view system analytics

### Teacher
- [ ] Can create assignments, tests, examinations
- [ ] Can grade student submissions
- [ ] Can view class performance
- [ ] Can post announcements

### Student
- [ ] Can view enrolled classes/subjects
- [ ] Can take/complete assessments
- [ ] Can view all types of results
- [ ] Can view termly and sessional results

### Parent
- [ ] Can link to children
- [ ] Can view children's academic data
- [ ] Can track children's progress

### General
- [ ] Application loads without errors
- [ ] All API endpoints return proper responses
- [ ] UI is responsive and user-friendly
- [ ] Role-based access control works correctly
