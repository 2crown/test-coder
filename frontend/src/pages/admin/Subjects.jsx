import { useEffect, useState } from 'react'
import api from '../../services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

export default function AdminSubjects() {
  const [subjects, setSubjects] = useState([])
  const [classes, setClasses] = useState([])
  const [teachers, setTeachers] = useState([])
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState({ name: '', code: '', class_id: '', teacher_id: '' })

  useEffect(() => { fetchData() }, [])

  const fetchData = async () => {
    try {
      const [subjectsRes, classesRes, teachersRes] = await Promise.all([
        api.get('/academic/subjects'),
        api.get('/academic/classes'),
        api.get('/admin/users').then(res => res.data.data || res.data).then(users => users.filter(u => u.roles?.[0]?.name === 'teacher'))
      ])
      setSubjects(subjectsRes.data.data || subjectsRes.data)
      setClasses(classesRes.data.data || classesRes.data)
      setTeachers(teachersRes)
    } catch (error) {
      console.error('Failed to fetch data:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    try {
      await api.post('/academic/subjects', formData)
      setShowForm(false)
      setFormData({ name: '', code: '', class_id: '', teacher_id: '' })
      fetchData()
    } catch (error) {
      console.error('Failed to create subject:', error)
    }
  }

  const handleDelete = async (id) => {
    if (!confirm('Delete this subject?')) return
    try {
      await api.delete(`/academic/subjects/${id}`)
      fetchData()
    } catch (error) {
      console.error('Failed to delete subject:', error)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold pl-[3rem] lg:pl-0">Subject Management</h1>
          <p className="text-muted-foreground">Manage subjects</p>
        </div>
        <Button onClick={() => setShowForm(!showForm)}>{showForm ? 'Cancel' : 'Add Subject'}</Button>
      </div>

      {showForm && (
        <Card>
          <CardHeader><CardTitle>Create Subject</CardTitle></CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label>Subject Name</Label>
                  <Input value={formData.name} onChange={(e) => setFormData({...formData, name: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>Code</Label>
                  <Input value={formData.code} onChange={(e) => setFormData({...formData, code: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>Class</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={formData.class_id} onChange={(e) => setFormData({...formData, class_id: e.target.value})} required>
                    <option value="">Select Class</option>
                    {classes.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Teacher (Optional)</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={formData.teacher_id} onChange={(e) => setFormData({...formData, teacher_id: e.target.value})}>
                    <option value="">Select Teacher</option>
                    {teachers.map(t => <option key={t.id} value={t.id}>{t.name}</option>)}
                  </select>
                </div>
              </div>
              <Button type="submit">Create</Button>
            </form>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-4 md:grid-cols-3">
        {subjects.map((subject) => (
          <Card key={subject.id}>
            <CardContent className="pt-6">
              <div className="flex justify-between items-start">
                <div>
                  <h3 className="font-semibold text-lg">{subject.name}</h3>
                  <p className="text-sm text-muted-foreground">Code: {subject.code}</p>
                  <p className="text-sm text-muted-foreground">Class: {subject.class_model?.name || subject.class_id}</p>
                </div>
                <Button variant="destructive" size="sm" onClick={() => handleDelete(subject.id)}>Delete</Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  )
}
