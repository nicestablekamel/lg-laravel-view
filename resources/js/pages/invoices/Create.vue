<script setup>
/**
 * Ported from the static prototype (index.html + style.css + script.js).
 * Template/PDF logic now lives in ../../lib/invoicePdf.js so History.vue
 * can reuse it to regenerate PDFs for previously saved invoices.
 */
import { computed, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { store as invoicesStore } from '@/routes/invoices'
import { invoiceTemplate, downloadInvoicePdf, printInvoiceWindow } from '../../lib/invoicePdf'

const props = defineProps({
  nextInvoiceNumber: { type: String, default: 'INV-0001' },
  parts: { type: Array, default: () => [] },
})

const defaultPolicyNote = `Cher(e) Client(e) :
Merci d'avoir choisit les produits LG Electronics.
Nos techniciens expérimentés ont procédé à la réparation de votre produit avec soin tout en utilisant des pièces originales LG.
Les produits réparés peuvent être stockés dans nos locaux pour une durée maximum de 03 Mois
LG Electronics ne sera plus responsable du Produit une fois les 03 mois écoulés.
Je reconnais avoir pris connaissance des conditions de prise en charge de mon produit en termes de service
en particulier dans la durée de récupération de mon produit qui ne saurait dépasser les trois (03) mois
Aussi je reconnais que le centre de service LG Electronics n'est plus responsable de mon produit une fois la période écoulée.
`

const form = useForm({
  company_name: 'Service après-vente LG',
  company_phone: '+213 776 60 78 48',
  company_email: 'service@example.com',
  company_address: 'Oran, Algeria',
  client_name: '',
  client_phone: '',
  reception_date: '',
  recuperation_date: '',
  product_category: 'Refrigerator',
  serial_number: '',
  problem_description: '',
  repair_quote: '',
  repair_status: 'Active',
  policy_note: defaultPolicyNote,
  parts: [],
})

const currentInvoiceNumber = ref(props.nextInvoiceNumber)
const generating = ref(false)

const availableParts = computed(() => props.parts ?? [])

function findPart(partId) {
  return availableParts.value.find((part) => String(part.id) === String(partId))
}

function addPartRow() {
  form.parts.push({ part_id: '', quantity: 1 })
}

function removePartRow(index) {
  form.parts.splice(index, 1)
}

const resolvedPartsUsed = computed(() =>
  form.parts
    .filter((row) => row.part_id)
    .map((row) => {
      const part = findPart(row.part_id)
      return {
        name: part ? part.name : '—',
        sku: part ? part.sku : '',
        quantity: row.quantity,
      }
    }),
)

const invoicePreviewHtml = computed(() =>
  invoiceTemplate({ ...form.data(), partsUsed: resolvedPartsUsed.value }, currentInvoiceNumber.value, false),
)

function resetForm() {
  form.reset()
}

async function handleSubmit() {
  generating.value = true

  try {
    const data = { ...form.data(), partsUsed: resolvedPartsUsed.value }

    await downloadInvoicePdf(data, currentInvoiceNumber.value, false)
    await new Promise((resolve) => setTimeout(resolve, 300))
    await downloadInvoicePdf(data, currentInvoiceNumber.value, true)

    form.post(invoicesStore(), {
      preserveScroll: true,
      onSuccess: (page) => {
        if (page.props.nextInvoiceNumber) {
          currentInvoiceNumber.value = page.props.nextInvoiceNumber
        }
        form.reset('client_name', 'client_phone', 'reception_date', 'recuperation_date', 'serial_number', 'problem_description', 'repair_quote', 'parts')
      },
    })
  } catch (error) {
    console.error(error)
    alert('Could not generate the PDFs. Check the browser console for details.')
  } finally {
    generating.value = false
  }
}

function printInvoice(repairman = false) {
  printInvoiceWindow({ ...form.data(), partsUsed: resolvedPartsUsed.value }, currentInvoiceNumber.value, repairman)
}
</script>

<template>
  <div class="app-shell">
    <header class="topbar">
      <div class="brand">
        <div class="brand-mark"><img src="/lglogo.png" alt="" /></div>
        <div>
          <h1>Service aprés-vente</h1>
          <p>facture de réparation</p>
        </div>
      </div>
      <div class="topbar-actions">
        <span class="status-dot"></span>
        Laravel + Vue
      </div>
    </header>

    <main class="workspace">
      <section class="panel form-panel">
        <div class="panel-heading">
          <div>
            <span class="eyebrow">Nouvelle Décharge</span>
            <h2>Créer une Décharge</h2>
          </div>
          <span class="invoice-badge">{{ currentInvoiceNumber }}</span>
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="section-title">Informations sur l'entreprise</div>
          <div class="grid two">
            <label>L'entreprise<input v-model="form.company_name" type="text" /></label>
            <label>Numéro<input v-model="form.company_phone" type="text" /></label>
            <label>Email<input v-model="form.company_email" type="email" /></label>
            <label>Addresse<input v-model="form.company_address" type="text" /></label>
          </div>

          <div class="section-title">Informations sur le client</div>
          <div class="grid two">
            <label>Nom et prénom *<input v-model="form.client_name" type="text" placeholder="Nom complet du client" required /></label>
            <label>Numéro de téléphone *<input v-model="form.client_phone" type="tel" placeholder="0555 00 00 00" required /></label>
            <label>Date de réception *<input v-model="form.reception_date" type="date" required /></label>
            <label>Date de récupération *<input v-model="form.recuperation_date" type="date" required /></label>
          </div>

          <div class="section-title">Produit</div>
          <div class="grid two">
            <label>
              Categorie *
              <select v-model="form.product_category" required>
                <option value="Refrigerator">réfrigérateur</option>
                <option value="Washing Machine">Machine à laver</option>
                <option value="Dishwasher">Lave-vaisselle</option>
              </select>
            </label>
            <label>Numéro de série *<input v-model="form.serial_number" type="text" placeholder="Numéro de série du produit" required /></label>
          </div>

          <div class="section-title">Détails de la réparation</div>
          <div class="grid two">
            <label class="full">Détails de la réparation *<textarea v-model="form.problem_description" rows="4" placeholder="Décrivez le problème signalé..." required></textarea></label>
            <label>Devis de réparation (DA) *<input v-model="form.repair_quote" type="number" min="0" step="1" placeholder="0" required /></label>

            <div class="field-group">
              <span class="label">État de la réparation *</span>
              <div class="status-options">
                <label class="radio-card" :class="{ active: form.repair_status === 'Active' }">
                  <input v-model="form.repair_status" type="radio" name="repairStatus" value="Active" />
                  <span>Active</span>
                </label>
                <label class="radio-card" :class="{ active: form.repair_status === 'Not Active' }">
                  <input v-model="form.repair_status" type="radio" name="repairStatus" value="Not Active" />
                  <span>Inactif</span>
                </label>
              </div>
            </div>
          </div>

          <div class="section-title">Pièces utilisées</div>
          <div class="parts-list">
            <p v-if="form.parts.length === 0" class="parts-empty">Aucune pièce ajoutée.</p>

            <div v-for="(row, index) in form.parts" :key="index" class="parts-row">
              <div>
                <select v-model="row.part_id">
                  <option value="" disabled>Choisir une pièce...</option>
                  <option v-for="part in availableParts" :key="part.id" :value="part.id">
                    {{ part.name }} ({{ part.quantity_on_hand }} {{ part.unit }} en stock)
                  </option>
                </select>
                <div v-if="findPart(row.part_id) && row.quantity > findPart(row.part_id).quantity_on_hand" class="stock-hint">
                  Stock insuffisant — {{ findPart(row.part_id).quantity_on_hand }} disponible(s).
                </div>
              </div>
              <input v-model.number="row.quantity" type="number" min="1" step="1" />
              <button type="button" class="remove-row-btn" @click="removePartRow(index)">×</button>
            </div>

            <button type="button" class="add-row-btn" @click="addPartRow">+ Ajouter une pièce</button>
            <div v-if="form.errors.parts" class="field-error">{{ form.errors.parts }}</div>
          </div>

          <div class="section-title">Politique / note</div>
          <label>Politique / note<textarea v-model="form.policy_note" rows="4"></textarea></label>

          <div class="form-actions">
            <button type="button" class="secondary-btn" @click="resetForm">Reset</button>
            <button type="submit" class="primary-btn" :disabled="generating">
              {{ generating ? 'Generating...' : 'Enregistrer et télécharger' }}
            </button>
          </div>

          <p class="hint">Le prototype génère localement, dans votre navigateur, une copie pour le client et une copie pour le réparateur, puis enregistre la décharge côté serveur.</p>
        </form>
      </section>

      <section class="panel preview-panel">
        <div class="panel-heading preview-heading">
          <div>
            <span class="eyebrow">Live preview</span>
            <h2>Client invoice</h2>
          </div>
          <div class="print-actions">
            <button class="icon-btn" type="button" @click="printInvoice(false)">Imprimer pour le client</button>
            <button class="icon-btn" type="button" @click="printInvoice(true)">Impression pour réparateur</button>
          </div>
        </div>

        <div class="preview-wrap">
          <div class="invoice-page" v-html="invoicePreviewHtml"></div>
        </div>
      </section>
    </main>
  </div>
</template>

<style>
@import '../../../css/invoice.css';
</style>
