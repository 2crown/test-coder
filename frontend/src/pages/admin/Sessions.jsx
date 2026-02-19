import { useEffect, useState } from 'react'
import api from '../../services/api'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

export default function AdminSessions() {
  const [sessions, setSessions] = useState([])
  const [terms, setTerms] = useState([])
  const [loading, setLoading] = useState(true)
  const [showSessionForm, setShowSessionForm] = useState(false)
  const [showTermForm, setShowTermForm] = useState(false)
  const [sessionData, setSessionData] = useState({ name: '', start_date: '', end_date: '' })
  const [termData, setTermData] = useState({ name: '', academic_session_id: '', start_date: '', end_date: '' })

  useEffect(() => { fetchData() }, [])

  const fetchData = async () => {
    try {
      const [sessionsRes, termsRes] = await Promise.all([
        api.get('/academic/sessions'),
        api.get('/academic/terms')
      ])
      setSessions(sessionsRes.data.data || sessionsRes.data)
      setTerms(termsRes.data.data || termsRes.data)
    } catch (error) {
      console.error('Failed to fetch data:', error)
    } finally {
      setLoading(false)
    }
  }

  const handleSessionSubmit = async (e) => {
    e.preventDefault()
    try {
      await api.post('/academic/sessions', sessionData)
      setShowSessionForm(false)
      setSessionData({ name: '', start_date: '', end_date: '' })
      fetchData()
    } catch (error) {
      console.error('Failed to create session:', error)
    }
  }

  const handleTermSubmit = async (e) => {
    e.preventDefault()
    try {
      await api.post('/academic/terms', termData)
      setShowTermForm(false)
      setTermData({ name: '', academic_session_id: '', start_date: '', end_date: '' })
      fetchData()
    } catch (error) {
      console.error('Failed to create term:', error)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold">Academic Sessions</h1>
        <p className="text-muted-foreground">Manage academic sessions and terms</p>
      </div>

      {/* Sessions */}
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">Sessions</h2>
        <Button onClick={() => setShowSessionForm(!showSessionForm)}>{showSessionForm ? 'Cancel' : 'Add Session'}</Button>
      </div>

      {showSessionForm && (
        <Card>
          <CardHeader><CardTitle>Create Academic Session</CardTitle></CardHeader>
          <CardContent>
            <form onSubmit={handleSessionSubmit} className="space-y-4">
              <div className="grid grid-cols-3 gap-4">
                <div className="space-y-2">
                  <Label>Session Name</Label>
                  <Input value={sessionData.name} onChange={(e) => setSessionData({...sessionData, name: e.target.value})} placeholder="e.g., 2024/2025" required />
                </div>
                <div className="space-y-2">
                  <Label>Start Date</Label>
                  <Input type="date" value={sessionData.start_date} onChange={(e) => setSessionData({...sessionData, start_date: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>End Date</Label>
                  <Input type="date" value={sessionData.end_date} onChange={(e) => setSessionData({...sessionData, end_date: e.target.value})} required />
                </div>
              </div>
              <Button type="submit">Create</Button>
            </form>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-4 md:grid-cols-3">
        {sessions.map((session) => (
          <Card key={session.id}>
            <CardContent className="pt-6">
              <div className="flex justify-between items-start">
                <div>
                  <h3 className="font-semibold text-lg">{session.name}</h3>
                  <p className="text-sm text-muted-foreground">
                    {session.start_date} - {session.end_date}
                  </p>
                  {session.is_current && <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Current</span>}
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {/* Terms */}
      <div className="flex justify-between items-center">
        <h2 className="text-xl font-semibold">Terms</h2>
        <Button onClick={() => setShowTermForm(!showTermForm)}>{showTermForm ? 'Cancel' : 'Add Term'}</Button>
      </div>

      {showTermForm && (
        <Card>
          <CardHeader><CardTitle>Create Term</CardTitle></CardHeader>
          <CardContent>
            <form onSubmit={handleTermSubmit} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <Label>Term Name</Label>
                  <Input value={termData.name} onChange={(e) => setTermData({...termData, name: e.target.value})} placeholder="e.g., First Term" required />
                </div>
                <div className="space-y-2">
                  <Label>Session</Label>
                  <select className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2" value={termData.academic_session_id} onChange={(e) => setTermData({...termData, academic_session_id: e.target.value})} required>
                    <option value="">Select Session</option>
                    {sessions.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                  </select>
                </div>
                <div className="space-y-2">
                  <Label>Start Date</Label>
                  <Input type="date" value={termData.start_date} onChange={(e) => setTermData({...termData, start_date: e.target.value})} required />
                </div>
                <div className="space-y-2">
                  <Label>End Date</Label>
                  <Input type="date" value={termData.end_date} onChange={(e) => setTermData({...termData, end_date: e.target.value})} required />
                </div>
              </div>
              <Button type="submit">Create</Button>
            </form>
          </CardContent>
        </Card>
      )}

      <div className="grid gap-4 md:grid-cols-3">
        {terms.map((term) => (
          <Card key={term.id}>
            <CardContent className="pt-6">
              <h3 className="font-semibold text-lg">{term.name}</h3>
              <p className="text-sm text-muted-foreground">
                {term.start_date} - {term.end_date}
              </p>
              <p className="text-sm text-muted-foreground">Session: {term.academic_session?.name}</p>
              {term.is_current && <span className="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Current</span>}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  )
}
