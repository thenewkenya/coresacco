import mpesa from './mpesa'
import airtel from './airtel'
import tkash from './tkash'

const webhooks = {
    mpesa: Object.assign(mpesa, mpesa),
    airtel: Object.assign(airtel, airtel),
    tkash: Object.assign(tkash, tkash),
}

export default webhooks