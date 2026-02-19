import { useEffect, useState } from 'react'
import api from '../../services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

export default function StudentAssessments() {
  const [assessments, setAssessments] = useState([])
  const [loading, setLoading] = useState(true)
  const [submitting, setSubmitting] = useState(null)
  const [content, setContent] = useState('')

  useEffect(() => { fetchAssessments() }, [])

  const fetchAssessments = async () => {
    try {
      const response = await api.get('/assessments')
      setAssessments(response.data.data || response.data)
    } catch (error) {
      console.error('Failed to fetch assessments:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (assessmentId) => {
    setSubmitting(assessmentId)
    try {
      await api.post(`/assessments/${assessmentId}/submit`, { content })
      alert('Submission successful!')
      setContent('')
      fetchAssessments()
    } catch (error) {
      console.error('Failed to submit:', error)
      alert('Failed to submit. Please try again.')
    } finally {
      setSubmitting(null)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  const getTypeColor = (type) => {
    switch (type) {
      case 'assignment': return 'bg-blue-100 text-blue-800'
      case 'test': return 'bg-yellow-100 text-yellow-800'
      case 'exam': return 'bg-red-100 text-red-800'
      default: return 'bg-gray-100 text-gray-800'
    }
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">My Assessments</h1>
        <p className="text-muted-foreground">View and take your assignments, tests, and exams</p>
      </div>

      <div className="space-y-4">
        {assessments.map((assessment) => (
          <Card key={assessment.id}>
            <CardHeader>
              <div className="flex justify-between items-start">
                <div>
                  <CardTitle className="text-lg">{assessment.title}</CardTitle>
                  <p className="text-sm text-muted-foreground mt-1">
                    {assessment.subject?.name} - {assessment.class_model?.name}
                  </p>
                </div>
                <span className={`px-3 py-1 rounded-full text-xs font-medium capitalize ${getTypeColor(assessment.type)}`}>
                  {assessment.type}
                </span>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="grid grid-cols-3 gap-4 text-sm">
                  <div>
                    <p className="text-muted-foreground">Total Marks</p>
                    <p className="font-medium">{assessment.total_marks}</p>
                  </div>
                  <div>
                    <p className="text-muted-foreground">Due Date</p>
                    <p className="font-medium">{assessment.due_date ? new Date(assessment.due_date).toLocaleString() : 'No deadline'}</p>
                  </div>
                  <div>
                    <p className="text-muted-foreground">Status</p>
                    <p className="font-medium">
                      {assessment.submissions?.[0]?.submitted_at ? 'Submitted' : 'Pending'}
                    </p>
                  </div>
                </div>

                {assessment.description && (
                  <div className="p-4 bg-gray-50 rounded-lg">
                    <p className="text-sm">{assessment.description}</p>
                  </div>
                )}

                {assessment.submissions?.[0]?.submitted_at ? (
                  <div className="p-4 bg-green-50 rounded-lg">
                    <p className="text-sm font-medium text-green-800">
                      ? Submitted on {new Date(assessment.submissions[0].submitted_at).toLocaleString()}
                    </p>
                    {assessment.submissions[0].marks !== null && (
                      <p className="text-sm text-green-700">
                        Marks: {assessment.submissions[0].marks}/{assessment.total_marks}
                      </p>
                    )}
                  </div>
                ) : (
                  <div className="space-y-3">
                    <div className="space-y-2">
                      <label className="text-sm font-medium">Your Answer</label>
                      <textarea
                        className="flex min-h-[100px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                        placeholder="Enter your answer here..."
                        value={content}
                        onChange={(e) => setContent(e.target.value)}
                      />
                    </div>
                    <Button onClick={() => handleSubmit(assessment.id)} disabled={submitting === assessment.id}>
                      {submitting === assessment.id ? 'Submitting...' : 'Submit'}
                    </Button>
                  </div>
                )}
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  )
}
