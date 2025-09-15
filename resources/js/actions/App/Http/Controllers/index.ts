import DashboardController from './DashboardController'
import MemberController from './MemberController'
import AccountController from './AccountController'
import TransactionController from './TransactionController'
import LoanController from './LoanController'
import LoanAccountController from './LoanAccountController'
import SavingsController from './SavingsController'
import NotificationsController from './NotificationsController'
import MobileMoneyWebhookController from './MobileMoneyWebhookController'
import Settings from './Settings'
import Auth from './Auth'

const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    MemberController: Object.assign(MemberController, MemberController),
    AccountController: Object.assign(AccountController, AccountController),
    TransactionController: Object.assign(TransactionController, TransactionController),
    LoanController: Object.assign(LoanController, LoanController),
    LoanAccountController: Object.assign(LoanAccountController, LoanAccountController),
    SavingsController: Object.assign(SavingsController, SavingsController),
    NotificationsController: Object.assign(NotificationsController, NotificationsController),
    MobileMoneyWebhookController: Object.assign(MobileMoneyWebhookController, MobileMoneyWebhookController),
    Settings: Object.assign(Settings, Settings),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers