import DashboardController from './DashboardController'
import TransactionController from './TransactionController'
import MobileMoneyWebhookController from './MobileMoneyWebhookController'
import Settings from './Settings'
import Auth from './Auth'

const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    TransactionController: Object.assign(TransactionController, TransactionController),
    MobileMoneyWebhookController: Object.assign(MobileMoneyWebhookController, MobileMoneyWebhookController),
    Settings: Object.assign(Settings, Settings),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers