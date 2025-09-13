import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:60
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
* @see routes/web.php:60
* @route '/savings'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:60
* @route '/savings'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:60
* @route '/savings'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:60
* @route '/savings'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:60
* @route '/savings'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:60
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
* @see routes/web.php:64
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
* @see routes/web.php:64
* @route '/savings/goals'
*/
goals.url = (options?: RouteQueryOptions) => {
    return goals.definition.url + queryParams(options)
}

/**
* @see routes/web.php:64
* @route '/savings/goals'
*/
goals.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: goals.url(options),
    method: 'get',
})

/**
* @see routes/web.php:64
* @route '/savings/goals'
*/
goals.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: goals.url(options),
    method: 'head',
})

/**
* @see routes/web.php:64
* @route '/savings/goals'
*/
const goalsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: goals.url(options),
    method: 'get',
})

/**
* @see routes/web.php:64
* @route '/savings/goals'
*/
goalsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: goals.url(options),
    method: 'get',
})

/**
* @see routes/web.php:64
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

const savings = {
    index: Object.assign(index, index),
    goals: Object.assign(goals, goals),
}

export default savings