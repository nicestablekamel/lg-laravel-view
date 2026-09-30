<script setup>
import { ref, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { index as invoicesIndex } from '@/routes/invoices'
import { downloadInvoicePdf, printInvoiceWindow, productLabels, formatMoney } from '../../lib/invoicePdf'

const props = defineProps({
  invoices: { type: Object, required: true }, // Laravel paginator: { data, links, ... }
  filters: { type: Object, default: () => ({ search: '' }) },
})

const search = ref(props.filters.search || '')
const downloading = ref(null) // `${id}-${copy}` while a PDF is generating

let debounceTimer = null
watch(search, (value) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    router.get(invoicesIndex(), { search: value }, { preserveState: true, preserveScroll: true, replace: true })
  }, 350)
})

function formatDate(value) {
  if (!value) return ''
  return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(value))
}

function productLabel(category) {
  return productLabels[category] || category
}

function partsSummary(invoice) {
  if (!invoice.parts || invoice.parts.length === 0) return '—'
  return invoice.parts.map((line) => `${line.part?.name ?? '—'} ×${line.quantity}`).join(', ')
}

function withPartsUsed(invoice) {
  const partsUsed = (invoice.parts ?? []).map((line) => ({
    name: line.part?.name ?? '—',
    sku: line.part?.sku ?? '',
    quantity: line.quantity,
  }))

  return { ...invoice, partsUsed }
}

async function handleDownload(invoice, repairman) {
  const key = `${invoice.id}-${repairman ? 'repairman' : 'client'}`
  downloading.value = key
  try {
    await downloadInvoicePdf(withPartsUsed(invoice), invoice.invoice_number, repairman)
  } catch (error) {
    console.error(error)
    alert('Could not regenerate the PDF. Check the browser console for details.')
  } finally {
    downloading.value = null
  }
}

function handlePrint(invoice, repairman) {
  printInvoiceWindow(withPartsUsed(invoice), invoice.invoice_number, repairman)
}
</script>

<template>
  <div class="history-page">
    <div class="history-card">
      <div class="history-toolbar">
        <h2>Historique des décharges</h2>
        <input
          v-model="search"
          type="search"
          class="history-search"
          placeholder="Rechercher (numéro, client, série)..."
        />
      </div>

      <div class="history-table-wrap">
        <table class="history-table">
          <thead>
            <tr>
              <th>Numéro</th>
              <th>Client</th>
              <th>Produit</th>
              <th>Devis</th>
              <th>État</th>
              <th>Pièces</th>
              <th>Créée le</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="invoice in invoices.data" :key="invoice.id">
              <td><strong>{{ invoice.invoice_number }}</strong></td>
              <td>{{ invoice.client_name }}</td>
              <td>{{ productLabel(invoice.product_category) }}</td>
              <td>{{ formatMoney(invoice.repair_quote) }}</td>
              <td>{{ invoice.repair_status === 'Active' ? 'Active' : 'Inactif' }}</td>
              <td>{{ partsSummary(invoice) }}</td>
              <td>{{ formatDate(invoice.created_at) }}</td>
              <td>
                <div class="history-actions">
                  <button
                    type="button"
                    :disabled="downloading === `${invoice.id}-client`"
                    @click="handleDownload(invoice, false)"
                  >
                    PDF client
                  </button>
                  <button
                    type="button"
                    :disabled="downloading === `${invoice.id}-repairman`"
                    @click="handleDownload(invoice, true)"
                  >
                    PDF réparateur
                  </button>
                  <button type="button" @click="handlePrint(invoice, false)">Imprimer</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <p v-if="invoices.data.length === 0" class="history-empty">
          Aucune décharge trouvée.
        </p>
      </div>

      <div class="history-pagination">
        <template v-for="(link, index) in invoices.links" :key="index">
          <span v-if="!link.url" class="disabled" v-html="link.label" />
          <Link
            v-else
            :href="link.url"
            :class="{ active: link.active }"
            preserve-scroll
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<style>
@import '../../../css/history.css';
</style>
