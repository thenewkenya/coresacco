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

const MobileMoneyWebhookController = { mpesaCallback }

export default MobileMoneyWebhookController