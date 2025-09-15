import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
const indexe5aa2cad321b30063c3b415df5452200 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexe5aa2cad321b30063c3b415df5452200.url(options),
    method: 'get',
})

indexe5aa2cad321b30063c3b415df5452200.definition = {
    methods: ["get","head"],
    url: '/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
indexe5aa2cad321b30063c3b415df5452200.url = (options?: RouteQueryOptions) => {
    return indexe5aa2cad321b30063c3b415df5452200.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
indexe5aa2cad321b30063c3b415df5452200.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexe5aa2cad321b30063c3b415df5452200.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
indexe5aa2cad321b30063c3b415df5452200.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexe5aa2cad321b30063c3b415df5452200.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
const indexe5aa2cad321b30063c3b415df5452200Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexe5aa2cad321b30063c3b415df5452200.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
indexe5aa2cad321b30063c3b415df5452200Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexe5aa2cad321b30063c3b415df5452200.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/transactions'
*/
indexe5aa2cad321b30063c3b415df5452200Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexe5aa2cad321b30063c3b415df5452200.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexe5aa2cad321b30063c3b415df5452200.form = indexe5aa2cad321b30063c3b415df5452200Form
/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
const index9fa68b3ceb04d1df189c74d7fe68cd33 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'get',
})

index9fa68b3ceb04d1df189c74d7fe68cd33.definition = {
    methods: ["get","head"],
    url: '/api/transactions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
index9fa68b3ceb04d1df189c74d7fe68cd33.url = (options?: RouteQueryOptions) => {
    return index9fa68b3ceb04d1df189c74d7fe68cd33.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
index9fa68b3ceb04d1df189c74d7fe68cd33.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
index9fa68b3ceb04d1df189c74d7fe68cd33.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
const index9fa68b3ceb04d1df189c74d7fe68cd33Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
index9fa68b3ceb04d1df189c74d7fe68cd33Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::index
* @see app/Http/Controllers/TransactionController.php:17
* @route '/api/transactions'
*/
index9fa68b3ceb04d1df189c74d7fe68cd33Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index9fa68b3ceb04d1df189c74d7fe68cd33.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index9fa68b3ceb04d1df189c74d7fe68cd33.form = index9fa68b3ceb04d1df189c74d7fe68cd33Form

export const index = {
    '/transactions': indexe5aa2cad321b30063c3b415df5452200,
    '/api/transactions': index9fa68b3ceb04d1df189c74d7fe68cd33,
}

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/transactions/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::create
* @see app/Http/Controllers/TransactionController.php:98
* @route '/transactions/create'
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
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/transactions'
*/
const storee5aa2cad321b30063c3b415df5452200 = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storee5aa2cad321b30063c3b415df5452200.url(options),
    method: 'post',
})

storee5aa2cad321b30063c3b415df5452200.definition = {
    methods: ["post"],
    url: '/transactions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/transactions'
*/
storee5aa2cad321b30063c3b415df5452200.url = (options?: RouteQueryOptions) => {
    return storee5aa2cad321b30063c3b415df5452200.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/transactions'
*/
storee5aa2cad321b30063c3b415df5452200.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storee5aa2cad321b30063c3b415df5452200.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/transactions'
*/
const storee5aa2cad321b30063c3b415df5452200Form = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storee5aa2cad321b30063c3b415df5452200.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/transactions'
*/
storee5aa2cad321b30063c3b415df5452200Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storee5aa2cad321b30063c3b415df5452200.url(options),
    method: 'post',
})

storee5aa2cad321b30063c3b415df5452200.form = storee5aa2cad321b30063c3b415df5452200Form
/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/api/transactions'
*/
const store9fa68b3ceb04d1df189c74d7fe68cd33 = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'post',
})

store9fa68b3ceb04d1df189c74d7fe68cd33.definition = {
    methods: ["post"],
    url: '/api/transactions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/api/transactions'
*/
store9fa68b3ceb04d1df189c74d7fe68cd33.url = (options?: RouteQueryOptions) => {
    return store9fa68b3ceb04d1df189c74d7fe68cd33.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/api/transactions'
*/
store9fa68b3ceb04d1df189c74d7fe68cd33.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/api/transactions'
*/
const store9fa68b3ceb04d1df189c74d7fe68cd33Form = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::store
* @see app/Http/Controllers/TransactionController.php:131
* @route '/api/transactions'
*/
store9fa68b3ceb04d1df189c74d7fe68cd33Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store9fa68b3ceb04d1df189c74d7fe68cd33.url(options),
    method: 'post',
})

store9fa68b3ceb04d1df189c74d7fe68cd33.form = store9fa68b3ceb04d1df189c74d7fe68cd33Form

export const store = {
    '/transactions': storee5aa2cad321b30063c3b415df5452200,
    '/api/transactions': store9fa68b3ceb04d1df189c74d7fe68cd33,
}

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
const show499a893674afb6a68aade9c8ace0c5be = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show499a893674afb6a68aade9c8ace0c5be.url(args, options),
    method: 'get',
})

show499a893674afb6a68aade9c8ace0c5be.definition = {
    methods: ["get","head"],
    url: '/transactions/{transaction}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
show499a893674afb6a68aade9c8ace0c5be.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return show499a893674afb6a68aade9c8ace0c5be.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
show499a893674afb6a68aade9c8ace0c5be.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show499a893674afb6a68aade9c8ace0c5be.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
show499a893674afb6a68aade9c8ace0c5be.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show499a893674afb6a68aade9c8ace0c5be.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
const show499a893674afb6a68aade9c8ace0c5beForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show499a893674afb6a68aade9c8ace0c5be.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
show499a893674afb6a68aade9c8ace0c5beForm.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show499a893674afb6a68aade9c8ace0c5be.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/transactions/{transaction}'
*/
show499a893674afb6a68aade9c8ace0c5beForm.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show499a893674afb6a68aade9c8ace0c5be.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show499a893674afb6a68aade9c8ace0c5be.form = show499a893674afb6a68aade9c8ace0c5beForm
/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
const showced43d88aacbce7eeb29263904881ea5 = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showced43d88aacbce7eeb29263904881ea5.url(args, options),
    method: 'get',
})

showced43d88aacbce7eeb29263904881ea5.definition = {
    methods: ["get","head"],
    url: '/api/transactions/{transaction}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
showced43d88aacbce7eeb29263904881ea5.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return showced43d88aacbce7eeb29263904881ea5.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
showced43d88aacbce7eeb29263904881ea5.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showced43d88aacbce7eeb29263904881ea5.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
showced43d88aacbce7eeb29263904881ea5.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showced43d88aacbce7eeb29263904881ea5.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
const showced43d88aacbce7eeb29263904881ea5Form = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showced43d88aacbce7eeb29263904881ea5.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
showced43d88aacbce7eeb29263904881ea5Form.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showced43d88aacbce7eeb29263904881ea5.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::show
* @see app/Http/Controllers/TransactionController.php:61
* @route '/api/transactions/{transaction}'
*/
showced43d88aacbce7eeb29263904881ea5Form.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showced43d88aacbce7eeb29263904881ea5.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showced43d88aacbce7eeb29263904881ea5.form = showced43d88aacbce7eeb29263904881ea5Form

export const show = {
    '/transactions/{transaction}': show499a893674afb6a68aade9c8ace0c5be,
    '/api/transactions/{transaction}': showced43d88aacbce7eeb29263904881ea5,
}

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
const receipt4387e993eb06826ada4541b2b4fb8d3f = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: receipt4387e993eb06826ada4541b2b4fb8d3f.url(args, options),
    method: 'get',
})

receipt4387e993eb06826ada4541b2b4fb8d3f.definition = {
    methods: ["get","head"],
    url: '/transactions/{transaction}/receipt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
receipt4387e993eb06826ada4541b2b4fb8d3f.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return receipt4387e993eb06826ada4541b2b4fb8d3f.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
receipt4387e993eb06826ada4541b2b4fb8d3f.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: receipt4387e993eb06826ada4541b2b4fb8d3f.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
receipt4387e993eb06826ada4541b2b4fb8d3f.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: receipt4387e993eb06826ada4541b2b4fb8d3f.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
const receipt4387e993eb06826ada4541b2b4fb8d3fForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receipt4387e993eb06826ada4541b2b4fb8d3f.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
receipt4387e993eb06826ada4541b2b4fb8d3fForm.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receipt4387e993eb06826ada4541b2b4fb8d3f.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/transactions/{transaction}/receipt'
*/
receipt4387e993eb06826ada4541b2b4fb8d3fForm.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receipt4387e993eb06826ada4541b2b4fb8d3f.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

receipt4387e993eb06826ada4541b2b4fb8d3f.form = receipt4387e993eb06826ada4541b2b4fb8d3fForm
/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
const receiptc0f6fa99944d6d0b02925a3c812756af = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: receiptc0f6fa99944d6d0b02925a3c812756af.url(args, options),
    method: 'get',
})

receiptc0f6fa99944d6d0b02925a3c812756af.definition = {
    methods: ["get","head"],
    url: '/api/transactions/{transaction}/receipt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
receiptc0f6fa99944d6d0b02925a3c812756af.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return receiptc0f6fa99944d6d0b02925a3c812756af.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
receiptc0f6fa99944d6d0b02925a3c812756af.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: receiptc0f6fa99944d6d0b02925a3c812756af.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
receiptc0f6fa99944d6d0b02925a3c812756af.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: receiptc0f6fa99944d6d0b02925a3c812756af.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
const receiptc0f6fa99944d6d0b02925a3c812756afForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receiptc0f6fa99944d6d0b02925a3c812756af.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
receiptc0f6fa99944d6d0b02925a3c812756afForm.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receiptc0f6fa99944d6d0b02925a3c812756af.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::receipt
* @see app/Http/Controllers/TransactionController.php:77
* @route '/api/transactions/{transaction}/receipt'
*/
receiptc0f6fa99944d6d0b02925a3c812756afForm.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: receiptc0f6fa99944d6d0b02925a3c812756af.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

receiptc0f6fa99944d6d0b02925a3c812756af.form = receiptc0f6fa99944d6d0b02925a3c812756afForm

export const receipt = {
    '/transactions/{transaction}/receipt': receipt4387e993eb06826ada4541b2b4fb8d3f,
    '/api/transactions/{transaction}/receipt': receiptc0f6fa99944d6d0b02925a3c812756af,
}

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/transactions/{transaction}/approve'
*/
const approve0ce9bd46465afe3be8f160063d1c3efa = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve0ce9bd46465afe3be8f160063d1c3efa.url(args, options),
    method: 'post',
})

approve0ce9bd46465afe3be8f160063d1c3efa.definition = {
    methods: ["post"],
    url: '/transactions/{transaction}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/transactions/{transaction}/approve'
*/
approve0ce9bd46465afe3be8f160063d1c3efa.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return approve0ce9bd46465afe3be8f160063d1c3efa.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/transactions/{transaction}/approve'
*/
approve0ce9bd46465afe3be8f160063d1c3efa.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve0ce9bd46465afe3be8f160063d1c3efa.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/transactions/{transaction}/approve'
*/
const approve0ce9bd46465afe3be8f160063d1c3efaForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve0ce9bd46465afe3be8f160063d1c3efa.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/transactions/{transaction}/approve'
*/
approve0ce9bd46465afe3be8f160063d1c3efaForm.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve0ce9bd46465afe3be8f160063d1c3efa.url(args, options),
    method: 'post',
})

approve0ce9bd46465afe3be8f160063d1c3efa.form = approve0ce9bd46465afe3be8f160063d1c3efaForm
/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/api/transactions/{transaction}/approve'
*/
const approve52950afc2a4b95a3f799eb494630d0e3 = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve52950afc2a4b95a3f799eb494630d0e3.url(args, options),
    method: 'post',
})

approve52950afc2a4b95a3f799eb494630d0e3.definition = {
    methods: ["post"],
    url: '/api/transactions/{transaction}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/api/transactions/{transaction}/approve'
*/
approve52950afc2a4b95a3f799eb494630d0e3.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return approve52950afc2a4b95a3f799eb494630d0e3.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/api/transactions/{transaction}/approve'
*/
approve52950afc2a4b95a3f799eb494630d0e3.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approve52950afc2a4b95a3f799eb494630d0e3.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/api/transactions/{transaction}/approve'
*/
const approve52950afc2a4b95a3f799eb494630d0e3Form = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve52950afc2a4b95a3f799eb494630d0e3.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::approve
* @see app/Http/Controllers/TransactionController.php:223
* @route '/api/transactions/{transaction}/approve'
*/
approve52950afc2a4b95a3f799eb494630d0e3Form.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: approve52950afc2a4b95a3f799eb494630d0e3.url(args, options),
    method: 'post',
})

approve52950afc2a4b95a3f799eb494630d0e3.form = approve52950afc2a4b95a3f799eb494630d0e3Form

export const approve = {
    '/transactions/{transaction}/approve': approve0ce9bd46465afe3be8f160063d1c3efa,
    '/api/transactions/{transaction}/approve': approve52950afc2a4b95a3f799eb494630d0e3,
}

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/transactions/{transaction}/reject'
*/
const rejectd184e74cc7a11b780d94badfd7d59cba = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rejectd184e74cc7a11b780d94badfd7d59cba.url(args, options),
    method: 'post',
})

rejectd184e74cc7a11b780d94badfd7d59cba.definition = {
    methods: ["post"],
    url: '/transactions/{transaction}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/transactions/{transaction}/reject'
*/
rejectd184e74cc7a11b780d94badfd7d59cba.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return rejectd184e74cc7a11b780d94badfd7d59cba.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/transactions/{transaction}/reject'
*/
rejectd184e74cc7a11b780d94badfd7d59cba.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rejectd184e74cc7a11b780d94badfd7d59cba.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/transactions/{transaction}/reject'
*/
const rejectd184e74cc7a11b780d94badfd7d59cbaForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rejectd184e74cc7a11b780d94badfd7d59cba.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/transactions/{transaction}/reject'
*/
rejectd184e74cc7a11b780d94badfd7d59cbaForm.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rejectd184e74cc7a11b780d94badfd7d59cba.url(args, options),
    method: 'post',
})

rejectd184e74cc7a11b780d94badfd7d59cba.form = rejectd184e74cc7a11b780d94badfd7d59cbaForm
/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/api/transactions/{transaction}/reject'
*/
const reject3c1f5193f5c76859b359791c0e136a0c = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject3c1f5193f5c76859b359791c0e136a0c.url(args, options),
    method: 'post',
})

reject3c1f5193f5c76859b359791c0e136a0c.definition = {
    methods: ["post"],
    url: '/api/transactions/{transaction}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/api/transactions/{transaction}/reject'
*/
reject3c1f5193f5c76859b359791c0e136a0c.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return reject3c1f5193f5c76859b359791c0e136a0c.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/api/transactions/{transaction}/reject'
*/
reject3c1f5193f5c76859b359791c0e136a0c.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject3c1f5193f5c76859b359791c0e136a0c.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/api/transactions/{transaction}/reject'
*/
const reject3c1f5193f5c76859b359791c0e136a0cForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject3c1f5193f5c76859b359791c0e136a0c.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TransactionController::reject
* @see app/Http/Controllers/TransactionController.php:257
* @route '/api/transactions/{transaction}/reject'
*/
reject3c1f5193f5c76859b359791c0e136a0cForm.post = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject3c1f5193f5c76859b359791c0e136a0c.url(args, options),
    method: 'post',
})

reject3c1f5193f5c76859b359791c0e136a0c.form = reject3c1f5193f5c76859b359791c0e136a0cForm

export const reject = {
    '/transactions/{transaction}/reject': rejectd184e74cc7a11b780d94badfd7d59cba,
    '/api/transactions/{transaction}/reject': reject3c1f5193f5c76859b359791c0e136a0c,
}

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
export const getStatus = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatus.url(args, options),
    method: 'get',
})

getStatus.definition = {
    methods: ["get","head"],
    url: '/api/transactions/{transaction}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
getStatus.url = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { transaction: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { transaction: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            transaction: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        transaction: typeof args.transaction === 'object'
        ? args.transaction.id
        : args.transaction,
    }

    return getStatus.definition.url
            .replace('{transaction}', parsedArgs.transaction.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
getStatus.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
getStatus.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getStatus.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
const getStatusForm = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
getStatusForm.get = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\TransactionController::getStatus
* @see app/Http/Controllers/TransactionController.php:278
* @route '/api/transactions/{transaction}/status'
*/
getStatusForm.head = (args: { transaction: number | { id: number } } | [transaction: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getStatus.form = getStatusForm

const TransactionController = { index, create, store, show, receipt, approve, reject, getStatus }

export default TransactionController