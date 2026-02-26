import { Routes, Route, Navigate } from "react-router-dom";
import { useSelector, useDispatch } from "react-redux";
import { useEffect } from "react";
import { fetchUser } from "./store/authSlice";
import { ToastProvider } from "@/components/ui/toast";

import Login from "./pages/auth/Login";
import Register from "./pages/auth/Register";
import AdminDashboard from "./pages/admin/Dashboard";
import AdminUsers from "./pages/admin/Users";
import AdminClasses from "./pages/admin/Classes";
import AdminSubjects from "./pages/admin/Subjects";
import AdminSessions from "./pages/admin/Sessions";
import TeacherDashboard from "./pages/teacher/Dashboard";
import TeacherAssessments from "./pages/teacher/Assessments";
import StudentDashboard from "./pages/student/Dashboard";
import StudentAssessments from "./pages/student/Assessments";
import StudentResults from "./pages/student/Results";
import ParentDashboard from "./pages/parent/Dashboard";
import Layout from "./components/Layout";
import { Button } from "./components/ui/button";

function ProtectedRoute({ children, allowedRoles }) {
  const { user, isAuthenticated, loading } = useSelector((state) => state.auth);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-screen">
        Loading...
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  if (allowedRoles && user) {
    const userRole = user?.roles?.[0];
    console.log("🔒 User role:", userRole);
    if (!allowedRoles.includes(userRole)) {
      return <Navigate to="/unauthorized" replace />;
    }
  }

  return children;
}

function App() {
  const dispatch = useDispatch();
  const { token, isAuthenticated, user, loading } = useSelector(
    (state) => state.auth,
  );

  useEffect(() => {
    if (token && !isAuthenticated) {
      dispatch(fetchUser());
    }
  }, [dispatch, token, isAuthenticated]);

  const getRedirectPath = (role) => {
    switch (role) {
      case "admin":
        return "/admin";
      case "teacher":
        return "/teacher";
      case "student":
        return "/student";
      case "parent":
        return "/parent";
      default:
        return "/";
    }
  };

  return (
    <ToastProvider>
      <Routes>
        <Route path="/login" element={<Login />} />
        <Route path="/register" element={<Register />} />
        <Route
          path="/unauthorized"
          element={
            <div className="min-h-screen flex flex-col gap-3 items-center justify-center">
              <h1 className="text-2xl font-bold">Unauthorized Access</h1>
              <Button onClick={() => (window.location.href = "/login")}>
                Go to Login
              </Button>
            </div>
          }
        />
        <Route
          path="/"
          element={
            loading ? (
              <div className="flex items-center justify-center h-screen">
                Loading...
              </div>
            ) : isAuthenticated && user ? (
              <ProtectedRoute>
                <Navigate to={getRedirectPath(user?.roles?.[0])} replace />
              </ProtectedRoute>
            ) : (
              <Navigate to="/login" replace />
            )
          }
        />

        <Route
          path="/admin"
          element={
            <ProtectedRoute allowedRoles={["admin"]}>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route index element={<AdminDashboard />} />
          <Route path="users" element={<AdminUsers />} />
          <Route path="classes" element={<AdminClasses />} />
          <Route path="subjects" element={<AdminSubjects />} />
          <Route path="sessions" element={<AdminSessions />} />
        </Route>

        <Route
          path="/teacher"
          element={
            <ProtectedRoute allowedRoles={["teacher"]}>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route index element={<TeacherDashboard />} />
          <Route path="/teacher/assessments" element={<TeacherAssessments />} />
        </Route>

        <Route
          path="/student"
          element={
            <ProtectedRoute allowedRoles={["student"]}>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route index element={<StudentDashboard />} />
          <Route path="assessments" element={<StudentAssessments />} />
          <Route path="results" element={<StudentResults />} />
        </Route>

        <Route
          path="/parent"
          element={
            <ProtectedRoute allowedRoles={["parent"]}>
              <Layout />
            </ProtectedRoute>
          }
        >
          <Route index element={<ParentDashboard />} />
        </Route>

        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </ToastProvider>
  );
}

export default App;
