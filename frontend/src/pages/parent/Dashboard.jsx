import { useEffect, useState } from 'react'
import api from '../../services/api'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Users, FileText, GraduationCap } from 'lucide-react'

export default function ParentDashboard() {
  const [data, setData] = useState(null)
  const [loading, setLoading] = useState(true)
  const [selectedChild, setSelectedChild] = useState(null)

  useEffect(() => { fetchDashboard() }, [])

  const fetchDashboard = async () => {
    try {
      const response = await api.get('/dashboard/parent')
      setData(response.data)
      if (response.data?.children?.length > 0) {
        setSelectedChild(response.data.children[0].student.id)
      }
    } catch (error) {
      console.error('Failed to fetch dashboard:', error)
    } finally {
      setLoading(false)
    }
  }

  if (loading) return <div className="flex items-center justify-center h-64">Loading...</div>

  const currentChild = data?.children?.find(c => c.student.id === selectedChild)

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold pl-[3rem] lg:pl-0">Parent Dashboard</h1>
        <p className="text-muted-foreground">Monitor your children&apos;s academic progress</p>
      </div>

      {/* Children Selector */}
      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Users className="h-5 w-5" />
            My Children
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="flex gap-2 flex-wrap">
            {data?.children?.map((child) => (
              <Button
                key={child.student.id}
                variant={selectedChild === child.student.id ? 'default' : 'outline'}
                onClick={() => setSelectedChild(child.student.id)}
              >
                {child.student.user?.name}
              </Button>
            ))}
          </div>
        </CardContent>
      </Card>

      {currentChild && (
        <>
          {/* Child Info */}
          <div className="grid gap-4 md:grid-cols-3">
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Student Name</CardTitle>
                <GraduationCap className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{currentChild.student.user?.name}</div>
                <p className="text-xs text-muted-foreground">
                  Class: {currentChild.student.class_model?.name}
                </p>
              </CardContent>
            </Card>
            <Card>
              <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                <CardTitle className="text-sm font-medium">Admission No.</CardTitle>
                <FileText className="h-4 w-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{currentChild.student.admission_number}</div>
              </CardContent>
            </Card>
          </div>

          {/* Recent Results */}
          <Card>
            <CardHeader>
              <CardTitle>Recent Results</CardTitle>
              <CardDescription>Latest academic performance</CardDescription>
            </CardHeader>
            <CardContent>
              {currentChild.recent_results?.length > 0 ? (
                <div className="space-y-4">
                  {currentChild.recent_results.map((result) => (
                    <div key={result.id} className="flex justify-between items-center p-4 border rounded-lg">
                      <div>
                        <p className="font-medium">{result.subject?.name}</p>
                        <p className="text-sm text-muted-foreground">{result.assessment?.title}</p>
                        <p className="text-xs text-muted-foreground capitalize">{result.assessment?.type}</p>
                      </div>
                      <div className="text-right">
                        <p className="text-2xl font-bold">{result.marks}</p>
                        <p className="text-sm font-medium text-green-600">{result.grade}</p>
                      </div>
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-muted-foreground">No results available yet</p>
              )}
            </CardContent>
          </Card>
        </>
      )}
    </div>
  )
}
