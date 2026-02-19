import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../../services/api'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Users, GraduationCap, BookOpen, Calendar, ClipboardList } from 'lucide-react'

export default function AdminDashboard() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => {
    fetchDashboard()
  }, [])

  const fetchDashboard = async () => {
    try {
      const response = await api.get('/dashboard/admin')
      setData(response.data)
    } catch (error) {
      console.error('Failed to fetch dashboard:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) {
    return <div className="flex items-center justify-center h-64">Loading...</div>
  }

  const stats = data?.stats || {}

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Admin Dashboard</h1>
        <p className="text-muted-foreground">Welcome back! Here's an overview of your school.</p>
      </div>

      {/* Stats Cards */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Students</CardTitle>
            <GraduationCap className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.total_students || 0}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Teachers</CardTitle>
            <Users className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.total_teachers || 0}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Total Classes</CardTitle>
            <BookOpen className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.total_classes || 0}</div>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Subjects</CardTitle>
            <ClipboardList className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{stats.total_subjects || 0}</div>
          </CardContent>
        </Card>
      </div>

      {/* Current Session Info */}
      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Current Academic Session</CardTitle>
            <CardDescription>Active session information</CardDescription>
          </CardHeader>
          <CardContent>
            {data?.current_session ? (
              <div className="space-y-2">
                <p className="font-medium">{data.current_session.name}</p>
                <p className="text-sm text-muted-foreground">
                  {new Date(data.current_session.start_date).toLocaleDateString()} - {new Date(data.current_session.end_date).toLocaleDateString()}
                </p>
              </div>
            ) : (
              <p className="text-muted-foreground">No active session</p>
            )}
          </CardContent>
        </Card>
        <Card>
          <CardHeader>
            <CardTitle>Current Term</CardTitle>
            <CardDescription>Active term information</CardDescription>
          </CardHeader>
          <CardContent>
            {data?.current_term ? (
              <div className="space-y-2">
                <p className="font-medium">{data.current_term.name}</p>
                <p className="text-sm text-muted-foreground">
                  {new Date(data.current_term.start_date).toLocaleDateString()} - {new Date(data.current_term.end_date).toLocaleDateString()}
                </p>
              </div>
            ) : (
              <p className="text-muted-foreground">No active term</p>
            )}
          </CardContent>
        </Card>
      </div>

      {/* Quick Actions */}
      <div className="grid gap-4 md:grid-cols-3">
        <Link to="/users">
          <Button variant="outline" className="w-full h-20">
            <Users className="h-5 w-5 mr-2" />
            Manage Users
          </Button>
        </Link>
        <Link to="/classes">
          <Button variant="outline" className="w-full h-20">
            <GraduationCap className="h-5 w-5 mr-2" />
            Manage Classes
          </Button>
        </Link>
        <Link to="/sessions">
          <Button variant="outline" className="w-full h-20">
            <Calendar className="h-5 w-5 mr-2" />
            Academic Sessions
          </Button>
        </Link>
      </div>
    </div>
  )
}
