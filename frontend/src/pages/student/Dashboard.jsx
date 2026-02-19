import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../../services/api'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { ClipboardList, FileText, Calendar } from 'lucide-react'

export default function StudentDashboard() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)

  useEffect(() => { fetchDashboard() }, [])

  const fetchDashboard = async () => {
    try {
      const response = await api.get('/dashboard/student')
      setData(response.data)
    } catch (error) {
      console.error('Failed to fetch dashboard:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Student Dashboard</h1>
        <p className="text-muted-foreground">Welcome back, {data?.student?.user?.name}!</p>
      </div>

      <div className="grid gap-4 md:grid-cols-3">
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Class</CardTitle>
            <ClipboardList className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{data?.student?.class_model?.name || 'N/A'}</div>
            <p className="text-xs text-muted-foreground">{data?.student?.class_model?.level}</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Current Term</CardTitle>
            <Calendar className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{data?.current_term?.name || 'N/A'}</div>
            <p className="text-xs text-muted-foreground">{data?.current_session?.name}</p>
          </CardContent>
        </Card>
        <Card>
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium">Subjects</CardTitle>
            <FileText className="h-4 w-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-bold">{data?.student?.subjects?.length || 0}</div>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle>Upcoming Assessments</CardTitle>
          </CardHeader>
          <CardContent>
            {data?.upcoming_assessments?.length > 0 ? (
              data.upcoming_assessments.map(assessment => (
                <div key={assessment.id} className="flex justify-between py-2 border-b">
                  <div>
                    <p className="font-medium">{assessment.title}</p>
                    <p className="text-sm text-muted-foreground capitalize">{assessment.type}</p>
                  </div>
                  <p className="text-sm text-muted-foreground">{new Date(assessment.due_date).toLocaleDateString()}</p>
                </div>
              ))
            ) : (
              <p className="text-muted-foreground">No upcoming assessments</p>
            )}
            <Link to="/student/assessments">
              <Button variant="outline" className="w-full mt-4">View All Assessments</Button>
            </Link>
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Recent Results</CardTitle>
          </CardHeader>
          <CardContent>
            {data?.recent_results?.length > 0 ? (
              data.recent_results.map(result => (
                <div key={result.id} className="flex justify-between py-2 border-b">
                  <div>
                    <p className="font-medium">{result.subject?.name}</p>
                    <p className="text-sm text-muted-foreground">{result.assessment?.title}</p>
                  </div>
                  <div className="text-right">
                    <p className="font-bold">{result.marks}</p>
                    <p className="text-sm text-muted-foreground">{result.grade}</p>
                  </div>
                </div>
              ))
            ) : (
              <p className="text-muted-foreground">No results yet</p>
            )}
            <Link to="/student/results">
              <Button variant="outline" className="w-full mt-4">View All Results</Button>
            </Link>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
