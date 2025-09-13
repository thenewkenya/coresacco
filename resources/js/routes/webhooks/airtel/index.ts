import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
export const callback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

callback.definition = {
    methods: ["post"],
    url: '/webhooks/airtel/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
callback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
const callbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::callback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
callbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: callback.url(options),
    method: 'post',
})

callback.form = callbackForm

const airtel = {
    callback: Object.assign(callback, callback),
}

export default airtel