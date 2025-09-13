import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::mpesaCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:22
* @route '/webhooks/mpesa/callback'
*/
export const mpesaCallback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mpesaCallback.url(options),
    method: 'post',
})

mpesaCallback.definition = {
    methods: ["post"],
    url: '/webhooks/mpesa/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::mpesaCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:22
* @route '/webhooks/mpesa/callback'
*/
mpesaCallback.url = (options?: RouteQueryOptions) => {
    return mpesaCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::mpesaCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:22
* @route '/webhooks/mpesa/callback'
*/
mpesaCallback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: mpesaCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::mpesaCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:22
* @route '/webhooks/mpesa/callback'
*/
const mpesaCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mpesaCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::mpesaCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:22
* @route '/webhooks/mpesa/callback'
*/
mpesaCallbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: mpesaCallback.url(options),
    method: 'post',
})

mpesaCallback.form = mpesaCallbackForm

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::airtelCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
export const airtelCallback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: airtelCallback.url(options),
    method: 'post',
})

airtelCallback.definition = {
    methods: ["post"],
    url: '/webhooks/airtel/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::airtelCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
airtelCallback.url = (options?: RouteQueryOptions) => {
    return airtelCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::airtelCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
airtelCallback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: airtelCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::airtelCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
const airtelCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: airtelCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::airtelCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:52
* @route '/webhooks/airtel/callback'
*/
airtelCallbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: airtelCallback.url(options),
    method: 'post',
})

airtelCallback.form = airtelCallbackForm

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::tkashCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
export const tkashCallback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tkashCallback.url(options),
    method: 'post',
})

tkashCallback.definition = {
    methods: ["post"],
    url: '/webhooks/tkash/callback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::tkashCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
tkashCallback.url = (options?: RouteQueryOptions) => {
    return tkashCallback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::tkashCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
tkashCallback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tkashCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::tkashCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
const tkashCallbackForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: tkashCallback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\MobileMoneyWebhookController::tkashCallback
* @see app/Http/Controllers/MobileMoneyWebhookController.php:82
* @route '/webhooks/tkash/callback'
*/
tkashCallbackForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: tkashCallback.url(options),
    method: 'post',
})

tkashCallback.form = tkashCallbackForm

const MobileMoneyWebhookController = { mpesaCallback, airtelCallback, tkashCallback }

export default MobileMoneyWebhookController