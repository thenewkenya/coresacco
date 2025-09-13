import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:69
* @route '/reports'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:69
* @route '/reports'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:69
* @route '/reports'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:69
* @route '/reports'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:69
* @route '/reports'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:69
* @route '/reports'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:69
* @route '/reports'
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
* @see routes/web.php:73
* @route '/reports/financial'
*/
export const financial = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: financial.url(options),
    method: 'get',
})

financial.definition = {
    methods: ["get","head"],
    url: '/reports/financial',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:73
* @route '/reports/financial'
*/
financial.url = (options?: RouteQueryOptions) => {
    return financial.definition.url + queryParams(options)
}

/**
* @see routes/web.php:73
* @route '/reports/financial'
*/
financial.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: financial.url(options),
    method: 'get',
})

/**
* @see routes/web.php:73
* @route '/reports/financial'
*/
financial.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: financial.url(options),
    method: 'head',
})

/**
* @see routes/web.php:73
* @route '/reports/financial'
*/
const financialForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: financial.url(options),
    method: 'get',
})

/**
* @see routes/web.php:73
* @route '/reports/financial'
*/
financialForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: financial.url(options),
    method: 'get',
})

/**
* @see routes/web.php:73
* @route '/reports/financial'
*/
financialForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: financial.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

financial.form = financialForm

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
export const members = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: members.url(options),
    method: 'get',
})

members.definition = {
    methods: ["get","head"],
    url: '/reports/members',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
members.url = (options?: RouteQueryOptions) => {
    return members.definition.url + queryParams(options)
}

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
members.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: members.url(options),
    method: 'get',
})

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
members.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: members.url(options),
    method: 'head',
})

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
const membersForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: members.url(options),
    method: 'get',
})

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
membersForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: members.url(options),
    method: 'get',
})

/**
* @see routes/web.php:77
* @route '/reports/members'
*/
membersForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: members.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

members.form = membersForm

const reports = {
    index: Object.assign(index, index),
    financial: Object.assign(financial, financial),
    members: Object.assign(members, members),
}

export default reports