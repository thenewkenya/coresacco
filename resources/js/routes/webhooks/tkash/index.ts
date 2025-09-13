import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
export const callback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

callback.definition = {
    methods: ["post"],
    url: '/webhooks/tkash/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
callback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
const callbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
callbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

callback.form = callbackForm

const tkash = {
    callback: Object.assign(callback, callback),
}

export default tkash