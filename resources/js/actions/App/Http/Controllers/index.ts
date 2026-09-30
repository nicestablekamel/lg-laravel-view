import InvoiceController from './InvoiceController'
import PartController from './PartController'
import Settings from './Settings'
const Controllers = {
    InvoiceController: Object.assign(InvoiceController, InvoiceController),
PartController: Object.assign(PartController, PartController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers