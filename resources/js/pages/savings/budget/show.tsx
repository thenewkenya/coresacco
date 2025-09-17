import { Head, Link } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Progress } from '@/components/ui/progress'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'
import { ArrowLeft, Calendar, DollarSign, PieChart, Target, TrendingUp } from 'lucide-react'
import { budget as budgetIndexUrl } from '@/routes/savings'
import { type BreadcrumbItem } from '@/types'
import { dashboard } from '@/routes'
import { index as savingsIndex } from '@/routes/savings'

interface BudgetItem {
  id: number
  category: string
  amount: number
  is_recurring: boolean
}

interface Budget {
  id: number
  month: number
  year: number
  total_income: number
  savings_target: number
  notes: string
  status: string
  items: BudgetItem[]
  user: {
    name: string
  }
}

interface Analysis {
  totalExpenses: number
  remainingIncome: number
  savingsAchieved: number
  savingsPercentage: number
}

interface ShowBudgetProps {
  budget: Budget
  analysis: Analysis
}

export default function ShowBudget({ budget, analysis }: ShowBudgetProps) {
  const getMonthName = (month: number) => {
    const months = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ]
    return months[month - 1] || 'Unknown'
  }

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Dashboard',
      href: dashboard().url,
    },
    {
      title: 'Savings',
      href: savingsIndex().url,
    },
    {
      title: 'Budget Planning',
      href: budgetIndexUrl().url,
    },
    {
      title: `${getMonthName(budget.month)} ${budget.year}`,
      href: '#',
    },
  ];

  const getStatusVariant = (status: string): "default" | "secondary" | "destructive" | "outline" => {
    switch (status) {
      case 'active':
        return 'default'
      case 'completed':
        return 'secondary'
      case 'cancelled':
        return 'destructive'
      default:
        return 'outline'
    }
  }

  const formatCurrency = (amount: number) => {
    return `KSh ${amount.toLocaleString()}`
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title={`Budget - ${getMonthName(budget.month)} ${budget.year}`} />
      
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            <Link href={budgetIndexUrl.url()}>
              <Button variant="outline" size="sm">
                <ArrowLeft className="h-4 w-4 mr-2" />
                Back to Budgets
              </Button>
            </Link>
            <div>
              <h1 className="text-3xl font-bold tracking-tight">
                Budget - {getMonthName(budget.month)} {budget.year}
              </h1>
              <p className="text-muted-foreground">
                Detailed view of your budget breakdown and analysis
              </p>
            </div>
          </div>
          <Badge variant={getStatusVariant(budget.status)}>
            {budget.status.charAt(0).toUpperCase() + budget.status.slice(1)}
          </Badge>
        </div>

        {/* Budget Overview Cards */}
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Total Income</CardTitle>
              <DollarSign className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{formatCurrency(budget.total_income)}</div>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Total Expenses</CardTitle>
              <PieChart className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{formatCurrency(analysis.totalExpenses)}</div>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Savings Target</CardTitle>
              <Target className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{formatCurrency(budget.savings_target)}</div>
              <p className="text-xs text-muted-foreground">
                {analysis.savingsPercentage.toFixed(1)}% of income
              </p>
            </CardContent>
          </Card>
          
          <Card>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">Remaining Income</CardTitle>
              <TrendingUp className="h-4 w-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className={`text-2xl font-bold ${
                analysis.remainingIncome >= 0 
                  ? 'text-green-600 dark:text-green-400' 
                  : 'text-red-600 dark:text-red-400'
              }`}>
                {formatCurrency(analysis.remainingIncome)}
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Budget Analysis */}
        <div className="grid gap-6 md:grid-cols-2">
          <Card>
            <CardHeader>
              <CardTitle>Savings Progress</CardTitle>
              <CardDescription>
                Track your progress towards your savings goal
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <div className="flex justify-between text-sm">
                  <span>Savings Target</span>
                  <span>{formatCurrency(budget.savings_target)}</span>
                </div>
                <Progress 
                  value={Math.min((analysis.savingsAchieved / budget.savings_target) * 100, 100)} 
                  className="h-2"
                />
                <div className="flex justify-between text-sm">
                  <span>Current Savings</span>
                  <span className={analysis.savingsAchieved >= 0 ? 'text-green-600' : 'text-red-600'}>
                    {formatCurrency(analysis.savingsAchieved)}
                  </span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card>
            <CardHeader>
              <CardTitle>Budget Summary</CardTitle>
              <CardDescription>
                Overview of your budget performance
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <div className="flex justify-between">
                  <span className="text-sm text-muted-foreground">Total Income</span>
                  <span className="font-medium">{formatCurrency(budget.total_income)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-muted-foreground">Total Expenses</span>
                  <span className="font-medium">{formatCurrency(analysis.totalExpenses)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-sm text-muted-foreground">Savings Target</span>
                  <span className="font-medium">{formatCurrency(budget.savings_target)}</span>
                </div>
                <hr />
                <div className="flex justify-between">
                  <span className="text-sm font-medium">Remaining Income</span>
                  <span className={`font-medium ${
                    analysis.remainingIncome >= 0 
                      ? 'text-green-600 dark:text-green-400' 
                      : 'text-red-600 dark:text-red-400'
                  }`}>
                    {formatCurrency(analysis.remainingIncome)}
                  </span>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        {/* Budget Items */}
        <Card>
          <CardHeader>
            <CardTitle>Budget Categories</CardTitle>
            <CardDescription>
              Breakdown of your budget allocations by category
            </CardDescription>
          </CardHeader>
          <CardContent>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Category</TableHead>
                  <TableHead>Amount</TableHead>
                  <TableHead>Percentage</TableHead>
                  <TableHead>Type</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {budget.items.map((item) => (
                  <TableRow key={item.id}>
                    <TableCell className="font-medium">{item.category}</TableCell>
                    <TableCell>{formatCurrency(item.amount)}</TableCell>
                    <TableCell>
                      {((item.amount / budget.total_income) * 100).toFixed(1)}%
                    </TableCell>
                    <TableCell>
                      <Badge variant={item.is_recurring ? 'default' : 'secondary'}>
                        {item.is_recurring ? 'Recurring' : 'One-time'}
                      </Badge>
                    </TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </CardContent>
        </Card>

        {/* Notes */}
        {budget.notes && (
          <Card>
            <CardHeader>
              <CardTitle>Notes</CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-sm text-muted-foreground whitespace-pre-wrap">
                {budget.notes}
              </p>
            </CardContent>
          </Card>
        )}
      </div>
    </AppLayout>
  )
}
