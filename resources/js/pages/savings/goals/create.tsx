import { Head, useForm, router } from '@inertiajs/react'
import AppLayout from '@/layouts/app-layout'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Checkbox } from '@/components/ui/checkbox'
import { DatePicker } from '@/components/ui/date-picker'
import { ArrowLeft, Target, DollarSign, FileText } from 'lucide-react'
import { create as createGoalUrl, store as storeGoalUrl } from '@/routes/savings/goals'
import { goals as goalsIndexUrl } from '@/routes/savings'

interface CreateGoalProps {
  auth: {
    user: any
  }
}

export default function CreateGoal({ auth }: CreateGoalProps) {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: '',
    description: '',
    target_amount: '',
    target_date: null as Date | null,
    category: '',
    priority: 'medium',
    auto_contribute: false,
    contribution_amount: '',
    contribution_frequency: 'monthly'
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    
    console.log('=== FORM SUBMISSION STARTED ===')
    console.log('Form data before processing:', data)
    
    // Format the data for submission - map frontend fields to backend fields
    const formData = {
      title: data.name,
      description: data.description,
      target_amount: parseFloat(data.target_amount) || 0,
      target_date: data.target_date ? data.target_date.toISOString().split('T')[0] : null,
      type: data.category,
      auto_save_amount: data.auto_contribute ? (parseFloat(data.contribution_amount) || 0) : null,
      auto_save_frequency: data.auto_contribute ? data.contribution_frequency : null,
    }
    
    console.log('Submitting goal data:', formData)
    console.log('Posting to URL:', storeGoalUrl.url())
    console.log('Processing state:', processing)
    
    // Use Inertia router directly with correct data format
    router.post(storeGoalUrl.url(), formData, {
      onSuccess: (page) => {
        console.log('Goal creation successful:', page)
        reset()
        window.location.href = goalsIndexUrl.url()
      },
      onError: (errors) => {
        console.error('Goal creation failed:', errors)
        console.error('Error details:', JSON.stringify(errors, null, 2))
      },
      onFinish: () => {
        console.log('Goal creation request finished')
      },
      onStart: () => {
        console.log('Goal creation request started')
      }
    })
    
    console.log('=== FORM SUBMISSION CALLED ===')
  }

  const goalCategories = [
    { value: 'emergency_fund', label: 'Emergency Fund' },
    { value: 'education', label: 'Education' },
    { value: 'home_purchase', label: 'Home Purchase' },
    { value: 'retirement', label: 'Retirement' },
    { value: 'custom', label: 'Custom' }
  ]

  const priorityLevels = [
    { value: 'low', label: 'Low Priority' },
    { value: 'medium', label: 'Medium Priority' },
    { value: 'high', label: 'High Priority' },
    { value: 'urgent', label: 'Urgent' }
  ]

  const contributionFrequencies = [
    { value: 'weekly', label: 'Weekly' },
    { value: 'biweekly', label: 'Bi-weekly' },
    { value: 'monthly', label: 'Monthly' },
    { value: 'quarterly', label: 'Quarterly' },
    { value: 'yearly', label: 'Yearly' }
  ]

  return (
    <AppLayout>
      <Head title="Create Savings Goal" />
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
              <h1 className="text-3xl font-bold tracking-tight">Create Savings Goal</h1>
              <p className="text-muted-foreground">
                Set up a new savings goal to track your financial progress
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
                  <Target className="h-5 w-5" />
                  <span>Goal Details</span>
                </CardTitle>
                <CardDescription>
                  Define your savings goal with specific targets and timeline
                </CardDescription>
              </CardHeader>
              <CardContent>
                <form onSubmit={handleSubmit} className="space-y-8">
                  {/* Goal Name */}
                  <div className="space-y-2">
                    <Label htmlFor="name">Goal Name *</Label>
                    <Input
                      id="name"
                      type="text"
                      value={data.name}
                      onChange={(e) => setData('name', e.target.value)}
                      placeholder="e.g., Emergency Fund, Vacation to Europe"
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
                      placeholder="Describe your goal and why it's important to you..."
                      rows={3}
                      className={errors.description ? 'border-red-500' : ''}
                    />
                    {errors.description && (
                      <p className="text-sm text-red-500">{errors.description}</p>
                    )}
                  </div>

                  {/* Target Amount and Date */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <Label htmlFor="target_amount">Target Amount (KES) *</Label>
                      <div className="relative">
                        <DollarSign className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                        <Input
                          id="target_amount"
                          type="number"
                          value={data.target_amount}
                          onChange={(e) => setData('target_amount', e.target.value)}
                          placeholder="0"
                          className={`pl-10 ${errors.target_amount ? 'border-red-500' : ''}`}
                          min="0"
                          step="0.01"
                        />
                      </div>
                      {errors.target_amount && (
                        <p className="text-sm text-red-500">{errors.target_amount}</p>
                      )}
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="target_date">Target Date *</Label>
                      <DatePicker
                        value={data.target_date}
                        onChange={(date) => setData('target_date', date)}
                        placeholder="Select target date"
                        className={errors.target_date ? 'border-red-500' : ''}
                      />
                      {errors.target_date && (
                        <p className="text-sm text-red-500">{errors.target_date}</p>
                      )}
                    </div>
                  </div>

                  {/* Category and Priority */}
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div className="space-y-2">
                      <Label htmlFor="category">Category *</Label>
                      <Select value={data.category} onValueChange={(value) => setData('category', value)}>
                        <SelectTrigger className={errors.category ? 'border-red-500' : ''}>
                          <SelectValue placeholder="Select a category" />
                        </SelectTrigger>
                        <SelectContent>
                          {goalCategories.map((category) => (
                            <SelectItem key={category.value} value={category.value}>
                              {category.label}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                      {errors.category && (
                        <p className="text-sm text-red-500">{errors.category}</p>
                      )}
                    </div>

                    <div className="space-y-2">
                      <Label htmlFor="priority">Priority Level</Label>
                      <Select value={data.priority} onValueChange={(value) => setData('priority', value)}>
                        <SelectTrigger>
                          <SelectValue placeholder="Select priority" />
                        </SelectTrigger>
                        <SelectContent>
                          {priorityLevels.map((priority) => (
                            <SelectItem key={priority.value} value={priority.value}>
                              {priority.label}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </div>
                  </div>

                  {/* Auto Contribution Settings */}
                  <div className="space-y-4">
                    <div className="flex items-center space-x-2">
                      <Checkbox
                        id="auto_contribute"
                        checked={data.auto_contribute}
                        onCheckedChange={(checked) => setData('auto_contribute', checked === true)}
                      />
                      <Label htmlFor="auto_contribute" className="text-sm font-medium">
                        Enable automatic contributions
                      </Label>
                    </div>

                    {data.auto_contribute && (
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4 pl-6 border-l-2 border-muted">
                        <div className="space-y-2">
                          <Label htmlFor="contribution_amount">Contribution Amount (KES)</Label>
                          <div className="relative">
                            <DollarSign className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                            <Input
                              id="contribution_amount"
                              type="number"
                              value={data.contribution_amount}
                              onChange={(e) => setData('contribution_amount', e.target.value)}
                              placeholder="0"
                              className="pl-10"
                              min="0"
                              step="0.01"
                            />
                          </div>
                        </div>

                        <div className="space-y-2">
                          <Label htmlFor="contribution_frequency">Frequency</Label>
                          <Select value={data.contribution_frequency} onValueChange={(value) => setData('contribution_frequency', value)}>
                            <SelectTrigger>
                              <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                              {contributionFrequencies.map((freq) => (
                                <SelectItem key={freq.value} value={freq.value}>
                                  {freq.label}
                                </SelectItem>
                              ))}
                            </SelectContent>
                          </Select>
                        </div>
                      </div>
                    )}
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
                    <Button type="submit" disabled={processing}>
                      {processing ? 'Creating...' : 'Create Goal'}
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
                  <FileText className="h-5 w-5" />
                  <span>Tips for Success</span>
                </CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Set Realistic Targets</h4>
                  <p className="text-sm text-muted-foreground">
                    Choose an amount and timeline that fits your current financial situation.
                  </p>
                </div>
                
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Use Categories</h4>
                  <p className="text-sm text-muted-foreground">
                    Categorizing your goals helps you prioritize and track different types of savings.
                  </p>
                </div>
                
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Enable Auto-Contributions</h4>
                  <p className="text-sm text-muted-foreground">
                    Automatic contributions help you stay consistent and reach your goals faster.
                  </p>
                </div>
                
                <div className="space-y-2">
                  <h4 className="font-medium text-sm">Review Regularly</h4>
                  <p className="text-sm text-muted-foreground">
                    Check your progress monthly and adjust your strategy as needed.
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
                  onClick={() => window.location.href = goalsIndexUrl.url()}
                >
                  <Target className="h-4 w-4 mr-2" />
                  View All Goals
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </AppLayout>
  )
}