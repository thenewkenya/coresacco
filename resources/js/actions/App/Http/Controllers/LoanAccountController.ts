import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/loan-accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LoanAccountController::index
* @see app/Http/Controllers/LoanAccountController.php:14
* @route '/loan-accounts'
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
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
export const show = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/loan-accounts/{loanAccount}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
show.url = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { loanAccount: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { loanAccount: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            loanAccount: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        loanAccount: typeof args.loanAccount === 'object'
        ? args.loanAccount.id
        : args.loanAccount,
    }

    return show.definition.url
            .replace('{loanAccount}', parsedArgs.loanAccount.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
show.get = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
show.head = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
const showForm = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
showForm.get = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LoanAccountController::show
* @see app/Http/Controllers/LoanAccountController.php:85
* @route '/loan-accounts/{loanAccount}'
*/
showForm.head = (args: { loanAccount: number | { id: number } } | [loanAccount: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const LoanAccountController = { index, show }

export default LoanAccountController