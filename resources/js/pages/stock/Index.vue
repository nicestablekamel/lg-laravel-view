<script setup>
import { reactive, ref, watch } from 'vue'
import { router, useForm } from '@inertiajs/vue3'
import { index as partsIndex, store as partsStore, update as partsUpdate, destroy as partsDestroy, restock as partsRestock } from '@/routes/parts'
import { formatMoney } from '../../lib/invoicePdf'

defineOptions({
  layout: {
    breadcrumbs: [
      {
        title: 'Stock',
        href: partsIndex(),
      },
    ],
  },
})


const props = defineProps({
  parts: { type: Array, required: true },
  filters: { type: Object, default: () => ({ search: '', low_stock: false }) },
})

const search = ref(props.filters.search || '')
const lowStockOnly = ref(Boolean(props.filters.low_stock))

let debounceTimer = null
watch(search, (value) => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => reload(value, lowStockOnly.value), 350)
})

watch(lowStockOnly, (value) => reload(search.value, value))

function reload(searchValue, lowStock) {
  router.get(
    partsIndex(),
    { search: searchValue, low_stock: lowStock ? 1 : 0 },
    { preserveState: true, preserveScroll: true, replace: true },
  )
}

const editingId = ref(null)

const form = useForm({
  name: '',
  sku: '',
  unit: 'pièce',
  quantity_on_hand: 0,
  low_stock_threshold: 0,
  unit_price: '',
  notes: '',
})

function startEdit(part) {
  editingId.value = part.id
  form.clearErrors()
  form.name = part.name
  form.sku = part.sku
  form.unit = part.unit
  form.low_stock_threshold = part.low_stock_threshold
  form.unit_price = part.unit_price ?? ''
  form.notes = part.notes ?? ''
}

function cancelEdit() {
  editingId.value = null
  form.reset()
  form.clearErrors()
}

function submit() {
  if (editingId.value) {
    form.put(partsUpdate(editingId.value), {
      preserveScroll: true,
      onSuccess: cancelEdit,
    })
  } else {
    form.post(partsStore(), {
      preserveScroll: true,
      onSuccess: () => form.reset('name', 'sku', 'quantity_on_hand', 'low_stock_threshold', 'unit_price', 'notes'),
    })
  }
}

const restockQuantities = reactive({})

watch(
  () => props.parts,
  (parts) => {
    parts.forEach((part) => {
      if (!(part.id in restockQuantities)) restockQuantities[part.id] = 1
    })
  },
  { immediate: true },
)

function handleRestock(part) {
  const quantity = Number(restockQuantities[part.id] || 1)
  if (quantity < 1) return

  router.post(partsRestock(part.id), { quantity }, {
    preserveScroll: true,
    onSuccess: () => {
      restockQuantities[part.id] = 1
    },
  })
}

function handleDelete(part) {
  if (!confirm(`Supprimer la pièce « ${part.name} » ?`)) return
  router.delete(partsDestroy(part.id), { preserveScroll: true })
}
</script>

<template>
  <div class="stock-page">
    <section class="stock-card">
      <h2>{{ editingId ? 'Modifier la pièce' : 'Ajouter une pièce' }}</h2>

      <form class="stock-form" @submit.prevent="submit">
        <label>
          Nom *
          <input v-model="form.name" type="text" required />
        </label>
        <div v-if="form.errors.name" class="field-error">{{ form.errors.name }}</div>

        <label>
          Référence (SKU) *
          <input v-model="form.sku" type="text" required />
        </label>
        <div v-if="form.errors.sku" class="field-error">{{ form.errors.sku }}</div>

        <div class="grid-two">
          <label>
            Unité
            <input v-model="form.unit" type="text" placeholder="pièce" />
          </label>
          <label>
            Prix unitaire (DA)
            <input v-model="form.unit_price" type="number" min="0" step="1" />
          </label>
        </div>

        <div class="grid-two">
          <label v-if="!editingId">
            Quantité initiale *
            <input v-model="form.quantity_on_hand" type="number" min="0" step="1" required />
          </label>
          <label>
            Seuil d'alerte *
            <input v-model="form.low_stock_threshold" type="number" min="0" step="1" required />
          </label>
        </div>
        <div v-if="form.errors.low_stock_threshold" class="field-error">{{ form.errors.low_stock_threshold }}</div>

        <label>
          Notes
          <textarea v-model="form.notes" rows="3"></textarea>
        </label>

        <div v-if="form.errors.part" class="field-error">{{ form.errors.part }}</div>

        <button type="submit" class="stock-submit-btn" :disabled="form.processing">
          {{ editingId ? 'Enregistrer les modifications' : 'Ajouter la pièce' }}
        </button>
        <button v-if="editingId" type="button" class="stock-cancel-btn" @click="cancelEdit">
          Annuler
        </button>
      </form>
    </section>

    <section class="stock-card">
      <div class="stock-toolbar">
        <h2>Inventaire des pièces</h2>
        <div class="stock-toolbar-controls">
          <label class="stock-toggle">
            <input v-model="lowStockOnly" type="checkbox" />
            Stock faible uniquement
          </label>
          <input v-model="search" type="search" class="stock-search" placeholder="Rechercher (nom, référence)..." />
        </div>
      </div>

      <div class="stock-table-wrap">
        <table class="stock-table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Référence</th>
              <th>Stock</th>
              <th>Seuil</th>
              <th>Prix</th>
              <th>Réappro.</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="part in parts" :key="part.id" :class="{ 'stock-row-low': part.is_low_stock }">
              <td>{{ part.name }}</td>
              <td>{{ part.sku }}</td>
              <td>
                <span class="stock-badge" :class="{ low: part.is_low_stock }">
                  {{ part.quantity_on_hand }} {{ part.unit }}
                </span>
              </td>
              <td>{{ part.low_stock_threshold }}</td>
              <td>{{ part.unit_price != null ? formatMoney(part.unit_price) : '—' }}</td>
              <td>
                <div class="stock-actions">
                  <input
                    v-model.number="restockQuantities[part.id]"
                    type="number"
                    min="1"
                    style="width: 56px"
                  />
                  <button type="button" @click="handleRestock(part)">+ Stock</button>
                </div>
              </td>
              <td>
                <div class="stock-actions">
                  <button type="button" @click="startEdit(part)">Modifier</button>
                  <button type="button" @click="handleDelete(part)">Supprimer</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <p v-if="parts.length === 0" class="stock-empty">Aucune pièce trouvée.</p>
      </div>
    </section>
  </div>
</template>

<style>
@import '../../../css/stock.css';
</style>
