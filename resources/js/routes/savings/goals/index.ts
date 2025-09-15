import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/savings/goals/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:303
* @route '/savings/goals/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/savings/goals',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:333
* @route '/savings/goals'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
export const show = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/savings/goals/{goal}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
show.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
show.get = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
show.head = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
const showForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showForm.get = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:387
* @route '/savings/goals/{goal}'
*/
showForm.head = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
export const update = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/savings/goals/{goal}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
update.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
update.put = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
const updateForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:413
* @route '/savings/goals/{goal}'
*/
updateForm.put = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\SavingsController::destroy
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
export const destroy = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/savings/goals/{goal}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SavingsController::destroy
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
destroy.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::destroy
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
destroy.delete = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SavingsController::destroy
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
const destroyForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::destroy
* @see app/Http/Controllers/SavingsController.php:443
* @route '/savings/goals/{goal}'
*/
destroyForm.delete = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\SavingsController::contribute
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
export const contribute = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: contribute.url(args, options),
    method: 'post',
})

contribute.definition = {
    methods: ["post"],
    url: '/savings/goals/{goal}/contribute',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SavingsController::contribute
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
contribute.url = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return contribute.definition.url
            .replace('{goal}', parsedArgs.goal.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::contribute
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
contribute.post = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: contribute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::contribute
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
const contributeForm = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: contribute.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::contribute
* @see app/Http/Controllers/SavingsController.php:461
* @route '/savings/goals/{goal}/contribute'
*/
contributeForm.post = (args: { goal: number | { id: number } } | [goal: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: contribute.url(args, options),
    method: 'post',
})

contribute.form = contributeForm

const goals = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    contribute: Object.assign(contribute, contribute),
}

export default goals