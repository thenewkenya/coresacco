import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Progress } from '@/components/ui/progress'
import { ArrowLeft, Target, DollarSign, Calendar, TrendingUp, User, FileText } from 'lucide-react'
import { goals as goalsIndexUrl } from '@/routes/savings'

interface Goal {
  id: number
  title: string
  description: string
  target_amount: number
  current_amount: number
  target_date: string
  status: 'active' | 'completed' | 'paused' | 'cancelled'
  type: 'emergency_fund' | 'education' | 'home_purchase' | 'retirement' | 'custom'
  auto_save_amount: number | null
  auto_save_frequency: 'weekly' | 'monthly' | 'quarterly' | 'yearly' | null
  member: {
    id: number
    name: string
    member_number: string
  }
  progress_percentage: number
  created_at: string
  updated_at: string
}

interface Props {
  goal: Goal
  progressPercentage: number
  daysRemaining: number
}

export default function GoalShow({ goal, progressPercentage, daysRemaining }: Props) {
  const getStatusVariant = (status: string): "default" | "secondary" | "destructive" | "outline" => {
    switch (status) {
      case 'active': return 'default'
      case 'completed': return 'secondary'
      case 'paused': return 'outline'
      case 'cancelled': return 'destructive'
      default: return 'outline'
    }
  }

  const getTypeVariant = (type: string): "default" | "secondary" | "destructive" | "outline" => {
    switch (type) {
      case 'emergency_fund': return 'default'
      case 'education': return 'secondary'
      case 'home_purchase': return 'default'
      case 'retirement': return 'secondary'
      case 'custom': return 'outline'
      default: return 'outline'
    }
  }

  const getTypeLabel = (type: string) => {
    switch (type) {
      case 'emergency_fund': return 'Emergency Fund'
      case 'education': return 'Education'
      case 'home_purchase': return 'Home Purchase'
      case 'retirement': return 'Retirement'
      case 'custom': return 'Custom'
      default: return type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
    }
  }

  const getFrequencyLabel = (frequency: string | null) => {
    if (!frequency) return 'None'
    switch (frequency) {
      case 'weekly': return 'Weekly'
      case 'monthly': return 'Monthly'
      case 'quarterly': return 'Quarterly'
      case 'yearly': return 'Yearly'
      default: return frequency
    }
  }

  const remainingAmount = goal.target_amount - goal.current_amount
  const isCompleted = goal.status === 'completed'
  const isOverdue = new Date(goal.target_date) < new Date() && !isCompleted

  return (
    <AppLayout>
      <Head title={`Goal - ${goal.title}`} />
      
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            <Link href={goalsIndexUrl.url()}>
              <Button variant="outline" size="sm">
                <ArrowLeft className="mr-2 h-4 w-4" /> Back to Goals
              </Button>
            </Link>
            <div>
              <h1 className="text-3xl font-bold tracking-tight">{goal.title}</h1>
              <p className="text-muted-foreground">
                {goal.description || 'No description provided'}
              </p>
            </div>
          </div>
          <div className="flex items-center space-x-2">
            <Badge variant={getStatusVariant(goal.status)}>
              {goal.status.charAt(0).toUpperCase() + goal.status.slice(1)}
            </Badge>
            <Badge variant={getTypeVariant(goal.type)}>
              {getTypeLabel(goal.type)}
            </Badge>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Target Amount</CardTitle>
              <Target className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">
                KSh {goal.target_amount.toLocaleString()}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Current Amount</CardTitle>
              <DollarSign className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">
                KSh {goal.current_amount.toLocaleString()}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Remaining</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className={`text-2xl font-bold ${remainingAmount <= 0 ? 'text-green-600' : 'text-muted-foreground'}`}>
                KSh {remainingAmount.toLocaleString()}
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Progress</CardTitle>
              <Calendar className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">
                {progressPercentage.toFixed(1)}%
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Progress and Details */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
          {/* Progress Card */}
          <Card className="lg:col-span-2">
            <CardHeader>
              <CardTitle className="flex items-center space-x-2">
                <TrendingUp className="h-5 w-5" />
                <span>Progress Overview</span>
              </CardTitle>
              <CardDescription>
                Track your progress towards achieving this goal
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-6">
              <div className="space-y-2">
                <div className="flex justify-between text-sm">
                  <span>Progress</span>
                  <span>{progressPercentage.toFixed(1)}%</span>
                </div>
                <Progress value={progressPercentage} className="h-3" />
              </div>

              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <div className="text-sm font-medium text-muted-foreground">Amount Saved</div>
                  <div className="text-2xl font-bold">
                    KSh {goal.current_amount.toLocaleString()}
                  </div>
                </div>
                <div className="space-y-2">
                  <div className="text-sm font-medium text-muted-foreground">Amount Remaining</div>
                  <div className={`text-2xl font-bold ${remainingAmount <= 0 ? 'text-green-600' : ''}`}>
                    KSh {remainingAmount.toLocaleString()}
                  </div>
                </div>
              </div>

              {isOverdue && (
                <Alert variant="destructive">
                  <Calendar className="h-4 w-4" />
                  <AlertDescription>
                    <div className="font-medium">This goal is overdue</div>
                    <div className="mt-1">
                      Target date was {new Date(goal.target_date).toLocaleDateString()}
                    </div>
                  </AlertDescription>
                </Alert>
              )}

              {isCompleted && (
                <Alert>
                  <Target className="h-4 w-4" />
                  <AlertDescription>
                    <div className="font-medium">Goal Completed! 🎉</div>
                    <div className="mt-1">
                      Congratulations on achieving your savings goal!
                    </div>
                  </AlertDescription>
                </Alert>
              )}
            </CardContent>
          </Card>

          {/* Goal Details Card */}
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center space-x-2">
                <FileText className="h-5 w-5" />
                <span>Goal Details</span>
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <div className="text-sm font-medium text-muted-foreground">Member</div>
                <div className="flex items-center space-x-2">
                  <User className="h-4 w-4" />
                  <div>
                    <div className="font-medium">{goal.member.name}</div>
                    <div className="text-sm text-muted-foreground">
                      {goal.member.member_number}
                    </div>
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <div className="text-sm font-medium text-muted-foreground">Target Date</div>
                <div className="flex items-center space-x-2">
                  <Calendar className="h-4 w-4" />
                  <span>{new Date(goal.target_date).toLocaleDateString()}</span>
                </div>
                {daysRemaining > 0 && (
                  <div className="text-sm text-muted-foreground">
                    {daysRemaining} days remaining
                  </div>
                )}
              </div>

              <div className="space-y-2">
                <div className="text-sm font-medium text-muted-foreground">Auto Save</div>
                <div>
                  {goal.auto_save_amount && goal.auto_save_frequency ? (
                    <div>
                      <div className="font-medium">
                        KSh {goal.auto_save_amount.toLocaleString()}
                      </div>
                      <div className="text-sm text-muted-foreground">
                        {getFrequencyLabel(goal.auto_save_frequency)}
                      </div>
                    </div>
                  ) : (
                    <span className="text-muted-foreground">Not enabled</span>
                  )}
                </div>
              </div>

              <div className="space-y-2">
                <div className="text-sm font-medium text-muted-foreground">Created</div>
                <div className="text-sm">
                  {new Date(goal.created_at).toLocaleDateString()}
                </div>
              </div>

              <div className="space-y-2">
                <div className="text-sm font-medium text-muted-foreground">Last Updated</div>
                <div className="text-sm">
                  {new Date(goal.updated_at).toLocaleDateString()}
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Actions */}
        <div className="flex justify-end space-x-4">
          <Link href={goalsIndexUrl.url()}>
            <Button variant="outline">
              Back to Goals
            </Button>
          </Link>
          <Button>
            Edit Goal
          </Button>
        </div>
      </div>
    </AppLayout>
  )
}
