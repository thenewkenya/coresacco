import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/savings/budget/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::create
* @see app/Http/Controllers/SavingsController.php:528
* @route '/savings/budget/create'
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
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/savings/budget',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SavingsController::store
* @see app/Http/Controllers/SavingsController.php:548
* @route '/savings/budget'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
export const show = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/savings/budget/{budget}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
show.url = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{budget}', parsedArgs.budget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
show.get = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
show.head = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
const showForm = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showForm.get = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SavingsController::show
* @see app/Http/Controllers/SavingsController.php:612
* @route '/savings/budget/{budget}'
*/
showForm.head = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
export const update = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/savings/budget/{budget}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
update.url = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{budget}', parsedArgs.budget.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
update.put = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\SavingsController::update
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
const updateForm = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see app/Http/Controllers/SavingsController.php:643
* @route '/savings/budget/{budget}'
*/
updateForm.put = (args: { budget: number | { id: number } } | [budget: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const budget = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
}

export default budget