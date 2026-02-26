import { useEffect, useState } from 'react'
import api from '../../services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

export default function TeacherAssessments() {
  const [assessments, setAssessments] = useState([])
  const [classes, setClasses] = useState([])
  const [subjects, setSubjects] = useState([])
  const [terms, setTerms] = useState([])
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState({
    title: '', type: 'assignment', subject_id: '', class_id: '', term_id: '', total_marks: 100, due_date: '', description: ''
  })

  useEffect(() => { fetchData() }, [])

  const fetchData = async () => {
    try {
      const [assessmentsRes, classesRes, subjectsRes, termsRes] = await Promise.all([
        api.get('/assessments'),
        api.get('/academic/classes'),
        api.get('/academic/subjects'),
        api.get('/academic/terms')
      ])
      setAssessments(assessmentsRes.data.data || assessmentsRes.data)
      setClasses(classesRes.data.data || classesRes.data)
      setSubjects(subjectsRes.data.data || subjectsRes.data)
      setTerms(termsRes.data.data || termsRes.data)
    } catch (error) {
      console.error('Failed to fetch data:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    try {
      await api.post('/assessments', formData)
      setShowForm(false)
      setFormData({ title: '', type: 'assignment', subject_id: '', class_id: '', term_id: '', total_marks: 100, due_date: '', description: '' })
      fetchData()
    } catch (error) {
      console.error('Failed to create assessment:', error)
    }
  }

  const handleDelete = async (id) => {
    if (!confirm('Delete this assessment?')) return
    try {
      await api.delete(`/assessments/${id}`)
      fetchData()
    } catch (error) {
      console.error('Failed to delete assessment:', error)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold pl-[3rem] lg:pl-0">Assessments</h1>
          <p className="text-muted-foreground">Create and manage assignments, tests, and exams</p>
        </div>
        <Button onClick={() => setShowForm(!showForm)}>{showForm ? 'Cancel' : 'Create Assessment'}</Button>
      </div>

      {showForm && (
        <Card>
          <CardHeader><CardTitle>Create New Assessment</CardTitle></CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label>Title</Label>
                  <Input value={formData.title} onChange={(e) => setFormData({...formData, title: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>Type</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={formData.type} onChange={(e) => setFormData({...formData, type: e.target.value})}>
                    <option value="assignment">Assignment</option>
                    <option value="test">Test</option>
                    <option value="exam">Exam</option>
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Subject</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={formData.subject_id} onChange={(e) => setFormData({...formData, subject_id: e.target.value})} required>
                    <option value="">Select Subject</option>
                    {subjects.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Class</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={formData.class_id} onChange={(e) => setFormData({...formData, class_id: e.target.value})} required>
                    <option value="">Select Class</option>
                    {classes.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Term</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={formData.term_id} onChange={(e) => setFormData({...formData, term_id: e.target.value})}>
                    <option value="">Select Term</option>
                    {terms.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Total Marks</Label>
                  <Input type="number" value={formData.total_marks} onChange={(e) => setFormData({...formData, total_marks: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>Due Date</Label>
                  <Input type="datetime-local" value={formData.due_date} onChange={(e) => setFormData({...formData, due_date: e.target.value})} />
                </div>
              </div>
              <div className="space-y-2">
                <Label>Description</Label>
                <textarea className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm" value={formData.description} onChange={(e) => setFormData({...formData, description: e.target.value})} />
              </div>
              <Button type="submit">Create</Button>
            </form>
          </CardContent>
        </Card>
      )}

      <div className="space-y-4">
        {assessments.map((assessment) => (
          <Card key={assessment.id}>
            <CardContent className="pt-6">
              <div className="flex justify-between items-start">
                <div>
                  <h3 className="font-semibold text-lg">{assessment.title}</h3>
                  <p className="text-sm text-muted-foreground capitalize">{assessment.type} - {assessment.subject?.name}</p>
                  <p className="text-sm text-muted-foreground">Class: {assessment.class_model?.name || assessment.class_id}</p>
                  <p className="text-sm text-muted-foreground">Marks: {assessment.total_marks}</p>
                  {assessment.due_date && <p className="text-sm text-muted-foreground">Due: {new Date(assessment.due_date).toLocaleString()}</p>}
                </div>
                <div className="flex gap-2">
                  <Button variant="outline" size="sm">View Submissions</Button>
                  <Button variant="destructive" size="sm" onClick={() => handleDelete(assessment.id)}>Delete</Button>
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  )
}
