import DashboardController from './DashboardController'
import InvoiceController from './InvoiceController'
import PartController from './PartController'
import Settings from './Settings'
const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
InvoiceController: Object.assign(InvoiceController, InvoiceController),
PartController: Object.assign(PartController, PartController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers