import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
const index42a740574ecbfbac32f8cc353fc32db9 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

index42a740574ecbfbac32f8cc353fc32db9.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
index42a740574ecbfbac32f8cc353fc32db9.url = (options?: RouteQueryOptions) => {
    return index42a740574ecbfbac32f8cc353fc32db9.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
index42a740574ecbfbac32f8cc353fc32db9.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
index42a740574ecbfbac32f8cc353fc32db9.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
const index42a740574ecbfbac32f8cc353fc32db9Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
index42a740574ecbfbac32f8cc353fc32db9Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index42a740574ecbfbac32f8cc353fc32db9.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/dashboard'
*/
index42a740574ecbfbac32f8cc353fc32db9Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index42a740574ecbfbac32f8cc353fc32db9.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index42a740574ecbfbac32f8cc353fc32db9.form = index42a740574ecbfbac32f8cc353fc32db9Form
/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
const index79e8db78b7285f47b9383df06923ad39 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index79e8db78b7285f47b9383df06923ad39.url(options),
    method: 'get',
})

index79e8db78b7285f47b9383df06923ad39.definition = {
    methods: ["get","head"],
    url: '/api/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
index79e8db78b7285f47b9383df06923ad39.url = (options?: RouteQueryOptions) => {
    return index79e8db78b7285f47b9383df06923ad39.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
index79e8db78b7285f47b9383df06923ad39.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index79e8db78b7285f47b9383df06923ad39.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
index79e8db78b7285f47b9383df06923ad39.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index79e8db78b7285f47b9383df06923ad39.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
const index79e8db78b7285f47b9383df06923ad39Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index79e8db78b7285f47b9383df06923ad39.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
index79e8db78b7285f47b9383df06923ad39Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index79e8db78b7285f47b9383df06923ad39.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::index
* @see app/Http/Controllers/DashboardController.php:19
* @route '/api/dashboard'
*/
index79e8db78b7285f47b9383df06923ad39Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index79e8db78b7285f47b9383df06923ad39.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index79e8db78b7285f47b9383df06923ad39.form = index79e8db78b7285f47b9383df06923ad39Form

export const index = {
    '/dashboard': index42a740574ecbfbac32f8cc353fc32db9,
    '/api/dashboard': index79e8db78b7285f47b9383df06923ad39,
}

const DashboardController = { index }

export default DashboardController