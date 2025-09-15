import { Head, useForm, router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { DateRangePicker } from '@/components/ui/date-range-picker'
import { ArrowLeft, Calculator, DollarSign, FileText, PieChart } from 'lucide-react'
import { create as createBudgetUrl, store as storeBudgetUrl } from '@/routes/savings/budget'
import { budget as budgetIndexUrl } from '@/routes/savings'

interface CreateBudgetProps {
  auth: {
    user: any
  }
}

export default function CreateBudget({ auth }: CreateBudgetProps) {
  console.log('CreateBudget - Auth data received:', auth)
  console.log('CreateBudget - User data:', auth?.user)
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    description: '',
    period: 'monthly',
    date_range: undefined as { from: Date; to?: Date } | undefined,
    total_income: '',
    categories: [
      { name: 'Housing', amount: '', percentage: 30 },
      { name: 'Food & Groceries', amount: '', percentage: 15 },
      { name: 'Transportation', amount: '', percentage: 10 },
      { name: 'Utilities', amount: '', percentage: 8 },
      { name: 'Healthcare', amount: '', percentage: 5 },
      { name: 'Entertainment', amount: '', percentage: 5 },
      { name: 'Savings', amount: '', percentage: 20 },
      { name: 'Miscellaneous', amount: '', percentage: 7 }
    ]
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    
    // Calculate total income and savings target
    const totalIncome = parseFloat(data.total_income) || 0
    const savingsTarget = totalIncome * 0.2 // 20% savings target
    
    // Format categories as items array
    let items = data.categories
      .filter(cat => cat.amount && parseFloat(cat.amount) > 0)
      .map(cat => ({
        category: cat.name,
        amount: parseFloat(cat.amount) || 0
      }))
    
    // Ensure we have at least one item (required by backend validation)
    if (items.length === 0) {
      items = [{
        category: 'General',
        amount: totalIncome * 0.1 // 10% general allocation
      }]
    }
    
    // Get month and year from date range
    const startDate = data.date_range?.from
    const month = startDate ? startDate.getMonth() + 1 : new Date().getMonth() + 1
    const year = startDate ? startDate.getFullYear() : new Date().getFullYear()
    
    // Format the data for submission
    const formData = {
      name: data.name,
      description: data.description,
      month: month,
      year: year,
      total_income: totalIncome,
      savings_target: savingsTarget,
      notes: data.description,
      items: items
    }
    
    console.log('=== UPDATED FORM CODE ===')
    console.log('Submitting budget data:', formData)
    console.log('Posting to URL:', storeBudgetUrl.url())
    console.log('Data structure check - has items:', !!formData.items)
    console.log('Data structure check - has categories:', !!formData.categories)
    console.log('Current URL:', window.location.href)
    console.log('Form data keys:', Object.keys(formData))
    
    // Use Inertia router with correct data format
    router.post(storeBudgetUrl.url(), formData, {
      onSuccess: () => {
        reset()
        window.location.href = budgetIndexUrl.url()
      },
      onError: (errors) => {
        console.error('Budget creation failed:', errors)
      }
    })
  }

  const handleCategoryChange = (index: number, field: 'amount' | 'percentage', value: string) => {
    const newCategories = [...data.categories]
    newCategories[index] = { ...newCategories[index], [field]: value }
    
    // If amount changed, calculate percentage
    if (field === 'amount' && data.total_income) {
      const amount = parseFloat(value) || 0
      const totalIncome = parseFloat(data.total_income) || 0
      const percentage = totalIncome > 0 ? ((amount / totalIncome) * 100).toFixed(1) : '0'
      newCategories[index].percentage = percentage
    }
    
    // If percentage changed, calculate amount
    if (field === 'percentage' && data.total_income) {
      const percentage = parseFloat(value) || 0
      const totalIncome = parseFloat(data.total_income) || 0
      const amount = ((percentage / 100) * totalIncome).toFixed(2)
      newCategories[index].amount = amount
    }
    
    setData('categories', newCategories)
  }

  const handleTotalIncomeChange = (value: string) => {
    setData('total_income', value)
    
    // Recalculate all amounts based on percentages
    const newCategories = data.categories.map(category => {
      const percentage = parseFloat(category.percentage.toString()) || 0
      const totalIncome = parseFloat(value) || 0
      const amount = ((percentage / 100) * totalIncome).toFixed(2)
      return { ...category, amount }
    })
    
    setData('categories', newCategories)
  }

  const budgetPeriods = [
    { value: 'weekly', label: 'Weekly' },
    { value: 'monthly', label: 'Monthly' },
    { value: 'quarterly', label: 'Quarterly' },
    { value: 'yearly', label: 'Yearly' }
  ]

  const totalAllocated = data.categories.reduce((sum, category) => {
    return sum + (parseFloat(category.percentage.toString()) || 0)
  }, 0)

  const remainingPercentage = 100 - totalAllocated

  return (
    <AppLayout>
      <Head title="Create Budget Plan" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
        {/* Header */}
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-4">
            <Button
              variant="outline"
              size="sm"
              onClick={() => window.history.back()}
              className="flex items-center space-x-2"
            >
              <ArrowLeft className="h-4 w-4" />
              <span>Back</span>
            </Button>
            <div>
              <h1 className="text-3xl font-bold tracking-tight">Create Budget Plan</h1>
              <p className="text-muted-foreground">
                Plan your finances with a comprehensive budget allocation
              </p>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Main Form */}
          <div className="lg:col-span-2">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <Calculator className="h-5 w-5" />
                  <span>Budget Details</span>
                </CardTitle>
                <CardDescription>
                  Set up your budget plan with income and expense categories
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleSubmit} className="space-y-8">
                  {/* Budget Name */}
                  <div className="space-y-2">
                    <Label htmlFor="name">Budget Name *</Label>
                    <Input
                      id="name"
                      type="text"
                      value={data.name}
                      onChange={(e) => setData('name', e.target.value)}
                      placeholder="e.g., Monthly Budget 2024, Emergency Budget"
                      className={errors.name ? 'border-red-500' : ''}
                    />
                    {errors.name && (
                      <p className="text-sm text-red-500">{errors.name}</p>
                    )}
                  </div>

                  {/* Description */}
                  <div className="space-y-2">
                    <Label htmlFor="description">Description</Label>
                    <Textarea
                      id="description"
                      value={data.description}
                      onChange={(e) => setData('description', e.target.value)}
                      placeholder="Describe your budget plan and financial goals..."
                      rows={3}
                      className={errors.description ? 'border-red-500' : ''}
                    />
                    {errors.description && (
                      <p className="text-sm text-red-500">{errors.description}</p>
                    )}
                  </div>

                  {/* Period and Dates */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <Label htmlFor="period">Budget Period *</Label>
                      <Select value={data.period} onValueChange={(value) => setData('period', value)}>
                        <SelectTrigger className={errors.period ? 'border-red-500' : ''}>
                          <SelectValue placeholder="Select period" />
                        </SelectTrigger>
                        <SelectContent>
                          {budgetPeriods.map((period) => (
                            <SelectItem key={period.value} value={period.value}>
                              {period.label}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      {errors.period && (
                        <p className="text-sm text-red-500">{errors.period}</p>
                      )}
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="date_range">Date Range *</Label>
                      <DateRangePicker
                        value={data.date_range}
                        onChange={(range) => setData('date_range', range)}
                        placeholder="Select date range"
                        className={errors.start_date || errors.end_date ? 'border-red-500' : ''}
                      />
                      {(errors.start_date || errors.end_date) && (
                        <p className="text-sm text-red-500">
                          {errors.start_date || errors.end_date || 'Please select a valid date range'}
                        </p>
                      )}
                    </div>
                  </div>

                  {/* Total Income */}
                  <div className="space-y-2">
                    <Label htmlFor="total_income">Total Income (KES) *</Label>
                    <div className="relative">
                      <DollarSign className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                      <Input
                        id="total_income"
                        type="number"
                        value={data.total_income}
                        onChange={(e) => handleTotalIncomeChange(e.target.value)}
                        placeholder="0"
                        className={`pl-10 ${errors.total_income ? 'border-red-500' : ''}`}
                        min="0"
                        step="0.01"
                      />
                    </div>
                    {errors.total_income && (
                      <p className="text-sm text-red-500">{errors.total_income}</p>
                    )}
                  </div>

                  {/* Budget Categories */}
                  <div className="space-y-4">
                    <div className="flex items-center justify-between">
                      <Label className="text-base font-medium">Budget Categories</Label>
                      <div className="text-sm text-muted-foreground">
                        Total Allocated: {totalAllocated.toFixed(1)}%
                        {remainingPercentage !== 0 && (
                          <span className={`ml-2 ${remainingPercentage < 0 ? 'text-red-500' : 'text-green-500'}`}>
                            ({remainingPercentage > 0 ? '+' : ''}{remainingPercentage.toFixed(1)}%)
                          </span>
                        )}
                      </div>
                    </div>

                    <div className="space-y-3">
                      {data.categories.map((category, index) => (
                        <div key={index} className="grid grid-cols-1 md:grid-cols-3 gap-3 p-3 border rounded-lg">
                          <div className="space-y-1">
                            <Label className="text-sm font-medium">{category.name}</Label>
                          </div>
                          
                          <div className="space-y-1">
                            <Label className="text-xs text-muted-foreground">Amount (KES)</Label>
                            <Input
                              type="number"
                              value={category.amount}
                              onChange={(e) => handleCategoryChange(index, 'amount', e.target.value)}
                              placeholder="0"
                              min="0"
                              step="0.01"
                              className="text-sm"
                            />
                          </div>
                          
                          <div className="space-y-1">
                            <Label className="text-xs text-muted-foreground">Percentage (%)</Label>
                            <Input
                              type="number"
                              value={category.percentage}
                              onChange={(e) => handleCategoryChange(index, 'percentage', e.target.value)}
                              placeholder="0"
                              min="0"
                              max="100"
                              step="0.1"
                              className="text-sm"
                            />
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>

                  {/* Submit Button */}
                  <div className="flex justify-end space-x-4">
                    <Button
                      type="button"
                      variant="outline"
                      onClick={() => window.history.back()}
                      disabled={processing}
                    >
                      Cancel
                    </Button>
                    <Button type="submit" disabled={processing || totalAllocated !== 100}>
                      {processing ? 'Creating...' : 'Create Budget'}
                    </Button>
                  </div>
                </form>
              </CardContent>
            </Card>
          </div>

          {/* Tips Sidebar */}
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center space-x-2">
                  <PieChart className="h-5 w-5" />
                  <span>Budget Tips</span>
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">50/30/20 Rule</h4>
                  <p className="text-sm text-muted-foreground">
                    50% needs, 30% wants, 20% savings and debt repayment.
                  </p>
                </div>
                
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Track Everything</h4>
                  <p className="text-sm text-muted-foreground">
                    Include all income sources and expense categories for accuracy.
                  </p>
                </div>
                
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Emergency Fund</h4>
                  <p className="text-sm text-muted-foreground">
                    Allocate 3-6 months of expenses to your emergency fund.
                  </p>
                </div>
                
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Review Monthly</h4>
                  <p className="text-sm text-muted-foreground">
                    Adjust your budget based on actual spending patterns.
                  </p>
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Quick Actions</CardTitle>
              </CardHeader>
              <CardContent className="space-y-2">
                <Button
                  variant="outline"
                  size="sm"
                  className="w-full justify-start"
                  onClick={() => window.location.href = budgetIndexUrl.url()}
                >
                  <PieChart className="h-4 w-4 mr-2" />
                  View All Budgets
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </AppLayout>
  )
}