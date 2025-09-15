import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import goals384f59 from './goals'
import budgetD46991 from './budget'
/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/savings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::index
* @see app/Http/Controllers/SavingsController.php:22
* @route '/savings'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
export const my = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: my.url(options),
    method: 'get',
})

my.definition = {
    methods: ["get","head"],
    url: '/savings/my',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
my.url = (options?: RouteQueryOptions) => {
    return my.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
my.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: my.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
my.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: my.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
const myForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: my.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
myForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: my.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::my
* @see app/Http/Controllers/SavingsController.php:102
* @route '/savings/my'
*/
myForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: my.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

my.form = myForm

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
export const goals = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: goals.url(options),
    method: 'get',
})

goals.definition = {
    methods: ["get","head"],
    url: '/savings/goals',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
goals.url = (options?: RouteQueryOptions) => {
    return goals.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
goals.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: goals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
goals.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: goals.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
const goalsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: goals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
goalsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: goals.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::goals
* @see app/Http/Controllers/SavingsController.php:165
* @route '/savings/goals'
*/
goalsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: goals.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

goals.form = goalsForm

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
export const budget = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: budget.url(options),
    method: 'get',
})

budget.definition = {
    methods: ["get","head"],
    url: '/savings/budget',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
budget.url = (options?: RouteQueryOptions) => {
    return budget.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
budget.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: budget.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
budget.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: budget.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
const budgetForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: budget.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
budgetForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: budget.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::budget
* @see app/Http/Controllers/SavingsController.php:244
* @route '/savings/budget'
*/
budgetForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: budget.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

budget.form = budgetForm

const savings = {
    index: Object.assign(index, index),
    my: Object.assign(my, my),
    goals: Object.assign(goals, goals384f59),
    budget: Object.assign(budget, budgetD46991),
}

export default savings