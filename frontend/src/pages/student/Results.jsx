import { useEffect, useState } from 'react'
import { Link } from 'react-router-dom'
import api from '../../services/api'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'

export default function StudentResults() {
  const [results, setResults] = useState([])
  const [terms, setTerms] = useState([])
  const [sessions, setSessions] = useState([])
  const [loading, setLoading] = useState(true)
  const [selectedTerm, setSelectedTerm] = useState(null)
  const [selectedSession, setSelectedSession] = useState(null)
  const [termResults, setTermResults] = useState(null)
  const [sessionResults, setSessionResults] = useState(null)

  useEffect(() => { fetchData() }, [])

  const fetchData = async () => {
    try {
      const [resultsRes, termsRes, sessionsRes] = await Promise.all([
        api.get('/results/student'),
        api.get('/academic/terms'),
        api.get('/academic/sessions')
      ])
      setResults(resultsRes.data.data || resultsRes.data)
      setTerms(termsRes.data.data || termsRes.data)
      setSessions(sessionsRes.data.data || sessionsRes.data)
    } catch (error) {
      console.error('Failed to fetch results:', error)
    } finally {
      setLoading(false)
    }
  }

  const fetchTermResults = async (termId) => {
    setSelectedTerm(termId)
    try {
      const response = await api.get(`/results/term/${termId}`)
      setTermResults(response.data)
    } catch (error) {
      console.error('Failed to fetch term results:', error)
    }
  }

  const fetchSessionResults = async (sessionId) => {
    setSelectedSession(sessionId)
    try {
      const response = await api.get(`/results/session/${sessionId}`)
      setSessionResults(response.data)
    } catch (error) {
      console.error('Failed to fetch session results:', error)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold pl-[3rem] lg:pl-0">My Results</h1>
        <p className="text-muted-foreground">View your test, assignment, and exam results</p>
      </div>

      <Tabs defaultValue="all" className="w-full">
        <TabsList>
          <TabsTrigger value="all">All Results</TabsTrigger>
          <TabsTrigger value="term">Termly Results</TabsTrigger>
          <TabsTrigger value="session">Sessional Results</TabsTrigger>
        </TabsList>

        <TabsContent value="all" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>All Results</CardTitle>
              <CardDescription>Complete result history</CardDescription>
            </CardHeader>
            <CardContent>
              {results.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="pb-3 text-left">Subject</th>
                        <th className="pb-3 text-left">Assessment</th>
                        <th className="pb-3 text-left">Type</th>
                        <th className="pb-3 text-left">Term</th>
                        <th className="pb-3 text-right">Marks</th>
                        <th className="pb-3 text-right">Grade</th>
                      </tr>
                    </thead>
                    <tbody>
                      {results.map((result) => (
                        <tr key={result.id} className="border-b">
                          <td className="py-3">{result.subject?.name}</td>
                          <td className="py-3">{result.assessment?.title}</td>
                          <td className="py-3 capitalize">{result.assessment?.type}</td>
                          <td className="py-3">{result.term?.name}</td>
                          <td className="py-3 text-right">{result.marks}</td>
                          <td className="py-3 text-right font-bold">{result.grade}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <p className="text-muted-foreground">No results available</p>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="term" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Termly Results</CardTitle>
              <CardDescription>Select a term to view results</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex gap-2 flex-wrap mb-4">
                {terms.map((term) => (
                  <button
                    key={term.id}
                    onClick={() => fetchTermResults(term.id)}
                    className={`px-4 py-2 rounded-lg text-sm font-medium ${
                      selectedTerm === term.id
                        ? 'bg-primary text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }`}
                  >
                    {term.name}
                  </button>
                ))}
              </div>

              {termResults && (
                <div className="mt-4">
                  <div className="grid grid-cols-3 gap-4 mb-4">
                    <div className="p-4 bg-blue-50 rounded-lg">
                      <p className="text-sm text-muted-foreground">Total Marks</p>
                      <p className="text-2xl font-bold">{termResults.total_marks}</p>
                    </div>
                    <div className="p-4 bg-green-50 rounded-lg">
                      <p className="text-sm text-muted-foreground">Average</p>
                      <p className="text-2xl font-bold">{termResults.average}</p>
                    </div>
                    <div className="p-4 bg-purple-50 rounded-lg">
                      <p className="text-sm text-muted-foreground">Subjects</p>
                      <p className="text-2xl font-bold">{termResults.subjects_count}</p>
                    </div>
                  </div>
                  <table className="w-full">
                    <thead>
                      <tr className="border-b">
                        <th className="pb-3 text-left">Subject</th>
                        <th className="pb-3 text-left">Assessment</th>
                        <th className="pb-3 text-right">Marks</th>
                        <th className="pb-3 text-right">Grade</th>
                      </tr>
                    </thead>
                    <tbody>
                      {termResults.results?.map((result) => (
                        <tr key={result.id} className="border-b">
                          <td className="py-3">{result.subject?.name}</td>
                          <td className="py-3">{result.assessment?.title}</td>
                          <td className="py-3 text-right">{result.marks}</td>
                          <td className="py-3 text-right font-bold">{result.grade}</td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="session" className="space-y-4">
          <Card>
            <CardHeader>
              <CardTitle>Sessional Results</CardTitle>
              <CardDescription>Select an academic session to view results</CardDescription>
            </CardHeader>
            <CardContent>
              <div className="flex gap-2 flex-wrap mb-4">
                {sessions.map((session) => (
                  <button
                    key={session.id}
                    onClick={() => fetchSessionResults(session.id)}
                    className={`px-4 py-2 rounded-lg text-sm font-medium ${
                      selectedSession === session.id
                        ? 'bg-primary text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }`}
                  >
                    {session.name}
                  </button>
                ))}
              </div>

              {sessionResults && (
                <div className="mt-4">
                  <div className="grid grid-cols-2 gap-4 mb-4">
                    <div className="p-4 bg-blue-50 rounded-lg">
                      <p className="text-sm text-muted-foreground">Overall Total</p>
                      <p className="text-2xl font-bold">{sessionResults.overall_total_marks}</p>
                    </div>
                    <div className="p-4 bg-green-50 rounded-lg">
                      <p className="text-sm text-muted-foreground">Overall Average</p>
                      <p className="text-2xl font-bold">{sessionResults.overall_average}</p>
                    </div>
                  </div>

                  {sessionResults.term_summaries?.map((summary, index) => (
                    <div key={index} className="mb-4">
                      <h4 className="font-semibold mb-2">{summary.term?.name}</h4>
                      <div className="grid grid-cols-3 gap-2 mb-2 text-sm">
                        <span>Total: {summary.total_marks}</span>
                        <span>Average: {summary.average}</span>
                        <span>Subjects: {summary.results.length}</span>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  )
}
