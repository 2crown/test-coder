import { useEffect, useState } from 'react'
import api from '../../services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

export default function AdminClasses() {
  const [classes, setClasses] = useState([])
  const [loading, setLoading] = useState(true)
  const [showForm, setShowForm] = useState(false)
  const [formData, setFormData] = useState({ name: '', level: '' })

  useEffect(() => { fetchClasses() }, [])

  const fetchClasses = async () => {
    try {
      const response = await api.get('/academic/classes')
      setClasses(response.data.data || response.data)
    } catch (error) {
      console.error('Failed to fetch classes:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSubmit = async (e) => {
    e.preventDefault()
    try {
      await api.post('/academic/classes', formData)
      setShowForm(false)
      setFormData({ name: '', level: '' })
      fetchClasses()
    } catch (error) {
      console.error('Failed to create class:', error)
    }
  }

  const handleDelete = async (id) => {
    if (!confirm('Delete this class?')) return
    try {
      await api.delete(`/academic/classes/${id}`)
      fetchClasses()
    } catch (error) {
      console.error('Failed to delete class:', error)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <div>
          <h1 className="text-3xl font-bold pl-[3rem] lg:pl-0">Class Management</h1>
          <p className="text-muted-foreground">Manage classes and grades</p>
        </div>
        <Button onClick={() => setShowForm(!showForm)}>{showForm ? 'Cancel' : 'Add Class'}</Button>
      </div>

      {showForm && (
        <Card>
          <CardHeader><CardTitle>Create Class</CardTitle></CardHeader>
          <CardContent>
            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label>Class Name</Label>
                  <Input value={formData.name} onChange={(e) => setFormData({...formData, name: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>Level</Label>
                  <Input value={formData.level} onChange={(e) => setFormData({...formData, level: e.target.value})} placeholder="e.g., JSS1, SS1" required />
                </div>
              </div>
              <Button type="submit">Create</Button>
            </form>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-4 md:grid-cols-3">
        {classes.map((cls) => (
          <Card key={cls.id}>
            <CardContent className="pt-6">
              <div className="flex justify-between items-start">
                <div>
                  <h3 className="font-semibold text-lg">{cls.name}</h3>
                  <p className="text-sm text-muted-foreground">Level: {cls.level}</p>
                </div>
                <Button variant="destructive" size="sm" onClick={() => handleDelete(cls.id)}>Delete</Button>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  )
}
