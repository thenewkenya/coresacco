import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
export const createGoal = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createGoal.url(options),
    method: 'get',
})

createGoal.definition = {
    methods: ["get","head"],
    url: '/savings/goals/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createGoal.url = (options?: RouteQueryOptions) => {
    return createGoal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createGoal.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createGoal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createGoal.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createGoal.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
const createGoalForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: createGoal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createGoalForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: createGoal.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::createGoal
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createGoalForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: createGoal.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

createGoal.form = createGoalForm

/**
* @see \App\Http\Controllers\SavingsController::storeGoal
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
export const storeGoal = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeGoal.url(options),
    method: 'post',
})

storeGoal.definition = {
    methods: ["post"],
    url: '/savings/goals',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SavingsController::storeGoal
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
storeGoal.url = (options?: RouteQueryOptions) => {
    return storeGoal.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::storeGoal
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
storeGoal.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeGoal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::storeGoal
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
const storeGoalForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeGoal.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::storeGoal
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
storeGoalForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeGoal.url(options),
    method: 'post',
})

storeGoal.form = storeGoalForm

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
export const showGoal = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showGoal.url(args, options),
    method: 'get',
})

showGoal.definition = {
    methods: ["get","head"],
    url: '/savings/goals/{goal}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showGoal.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { goal: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { goal: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            goal: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        goal: typeof args.goal === 'object'
        ? args.goal.id
        : args.goal,
    }

    return showGoal.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showGoal.get = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showGoal.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showGoal.head = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showGoal.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
const showGoalForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showGoal.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showGoalForm.get = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showGoal.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::showGoal
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showGoalForm.head = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showGoal.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showGoal.form = showGoalForm

/**
* @see \App\Http\Controllers\SavingsController::updateGoal
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
export const updateGoal = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateGoal.url(args, options),
    method: 'put',
})

updateGoal.definition = {
    methods: ["put"],
    url: '/savings/goals/{goal}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\SavingsController::updateGoal
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
updateGoal.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { goal: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { goal: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            goal: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        goal: typeof args.goal === 'object'
        ? args.goal.id
        : args.goal,
    }

    return updateGoal.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::updateGoal
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
updateGoal.put = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateGoal.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\SavingsController::updateGoal
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
const updateGoalForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateGoal.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::updateGoal
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
updateGoalForm.put = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateGoal.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateGoal.form = updateGoalForm

/**
* @see \App\Http\Controllers\SavingsController::destroyGoal
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
export const destroyGoal = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyGoal.url(args, options),
    method: 'delete',
})

destroyGoal.definition = {
    methods: ["delete"],
    url: '/savings/goals/{goal}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SavingsController::destroyGoal
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
destroyGoal.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { goal: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { goal: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            goal: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        goal: typeof args.goal === 'object'
        ? args.goal.id
        : args.goal,
    }

    return destroyGoal.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::destroyGoal
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
destroyGoal.delete = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyGoal.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SavingsController::destroyGoal
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
const destroyGoalForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyGoal.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::destroyGoal
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
destroyGoalForm.delete = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroyGoal.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroyGoal.form = destroyGoalForm

/**
* @see \App\Http\Controllers\SavingsController::contributeToGoal
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
export const contributeToGoal = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: contributeToGoal.url(args, options),
    method: 'post',
})

contributeToGoal.definition = {
    methods: ["post"],
    url: '/savings/goals/{goal}/contribute',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SavingsController::contributeToGoal
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
contributeToGoal.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { goal: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { goal: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            goal: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        goal: typeof args.goal === 'object'
        ? args.goal.id
        : args.goal,
    }

    return contributeToGoal.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::contributeToGoal
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
contributeToGoal.post = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: contributeToGoal.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::contributeToGoal
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
const contributeToGoalForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: contributeToGoal.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::contributeToGoal
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
contributeToGoalForm.post = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: contributeToGoal.url(args, options),
    method: 'post',
})

contributeToGoal.form = contributeToGoalForm

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
export const createBudget = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createBudget.url(options),
    method: 'get',
})

createBudget.definition = {
    methods: ["get","head"],
    url: '/savings/budget/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
createBudget.url = (options?: RouteQueryOptions) => {
    return createBudget.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
createBudget.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createBudget.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
createBudget.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createBudget.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
const createBudgetForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: createBudget.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
createBudgetForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: createBudget.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::createBudget
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
createBudgetForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: createBudget.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

createBudget.form = createBudgetForm

/**
* @see \App\Http\Controllers\SavingsController::storeBudget
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
export const storeBudget = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeBudget.url(options),
    method: 'post',
})

storeBudget.definition = {
    methods: ["post"],
    url: '/savings/budget',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SavingsController::storeBudget
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
storeBudget.url = (options?: RouteQueryOptions) => {
    return storeBudget.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::storeBudget
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
storeBudget.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeBudget.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::storeBudget
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
const storeBudgetForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeBudget.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::storeBudget
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
storeBudgetForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeBudget.url(options),
    method: 'post',
})

storeBudget.form = storeBudgetForm

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
export const showBudget = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showBudget.url(args, options),
    method: 'get',
})

showBudget.definition = {
    methods: ["get","head"],
    url: '/savings/budget/{budget}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showBudget.url = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { budget: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { budget: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            budget: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        budget: typeof args.budget === 'object'
        ? args.budget.id
        : args.budget,
    }

    return showBudget.definition.url
            .replace('{budget}', parsedArgs.budget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showBudget.get = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showBudget.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showBudget.head = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showBudget.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
const showBudgetForm = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showBudget.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showBudgetForm.get = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showBudget.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::showBudget
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showBudgetForm.head = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showBudget.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showBudget.form = showBudgetForm

/**
* @see \App\Http\Controllers\SavingsController::updateBudget
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
export const updateBudget = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateBudget.url(args, options),
    method: 'put',
})

updateBudget.definition = {
    methods: ["put"],
    url: '/savings/budget/{budget}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\SavingsController::updateBudget
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
updateBudget.url = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { budget: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { budget: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            budget: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        budget: typeof args.budget === 'object'
        ? args.budget.id
        : args.budget,
    }

    return updateBudget.definition.url
            .replace('{budget}', parsedArgs.budget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::updateBudget
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
updateBudget.put = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateBudget.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\SavingsController::updateBudget
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
const updateBudgetForm = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateBudget.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::updateBudget
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
updateBudgetForm.put = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateBudget.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateBudget.form = updateBudgetForm

const SavingsController = { index, my, goals, budget, createGoal, storeGoal, showGoal, updateGoal, destroyGoal, contributeToGoal, createBudget, storeBudget, showBudget, updateBudget }

export default SavingsController