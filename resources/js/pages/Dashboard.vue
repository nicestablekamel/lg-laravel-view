<script setup>
/**
 * NOTE: this replaces the starter kit's default resources/js/pages/Dashboard.vue.
 * If your project's AppLayout import path or breadcrumb prop shape differs
 * from what's used below, adjust — this assumes the standard starter kit
 * layout (see README-dashboard.md).
 */
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { create as invoicesCreate, index as invoicesIndex } from '@/routes/invoices'
import { index as partsIndex } from '@/routes/parts'
import { dashboard } from '@/routes'
import { Box, Plus } from '@lucide/vue'
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Tableau de bord',
                href: dashboard(),
            },
        ],
    },
});

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({ total_repairs: 0, active_repairs: 0, inactive_repairs: 0, revenue: 0 }),
  },
  recentRepairs: { type: Array, default: () => [] },
})

const breadcrumbs = [{ title: 'Dashboard', href: '/dashboard' }]

const productLabels = {
  Refrigerator: 'réfrigérateur',
  'Washing Machine': 'Machine à laver',
  Dishwasher: 'Lave-vaisselle',
}

function productLabel(category) {
  return productLabels[category] || category
}

function formatMoney(value) {
  return new Intl.NumberFormat('fr-DZ', { maximumFractionDigits: 0 }).format(Number(value || 0)) + ' DA'
}

function formatDate(value) {
  if (!value) return ''
  return new Intl.DateTimeFormat('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(new Date(value))
}
</script>

<template>
    <div class="dash-page">
      <div class="dash-quick-actions">
        <Link :href="invoicesCreate()" class="dash-quick-action m-2 transition-all duration-200
           hover:-translate-y-0.5 hover:shadow-md
           active:scale-95">
          <Plus />
          Nouvelle décharge
        </Link>
        <Link :href="partsIndex()" class="dash-quick-action m-2 transition-all duration-200
           hover:-translate-y-0.5 hover:shadow-md
           active:scale-95">
          <Box />
          Stock
        </Link>
      </div>

      <div class="dash-kpis m-3">
        <div class="dash-kpi-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg">
          <p class="dash-kpi-label">Total réparations</p>
          <p class="dash-kpi-value">{{ stats.total_repairs }}</p>
        </div>
        <div class="dash-kpi-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg">
          <p class="dash-kpi-label">Réparations actives</p>
          <p class="dash-kpi-value">{{ stats.active_repairs }}</p>
        </div>
        <div class="dash-kpi-card transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg">
          <p class="dash-kpi-label">Réparations inactives</p>
          <p class="dash-kpi-value">{{ stats.inactive_repairs }}</p>
        </div>
        <div class="dash-kpi-card accent transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg">
          <p class="dash-kpi-label">Chiffre d'affaires</p>
          <p class="dash-kpi-value">{{ formatMoney(stats.revenue) }}</p>
        </div>
      </div>

      <div class="dash-card m-3 transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg">
        <div class="dash-card-heading">
          <h2>Réparations récentes</h2>
          <Link :href="invoicesIndex()" class="inv-link transition-all duration-200">
            Voir tout l'historique →
          </Link>
        </div>

        <div class="dash-table-wrap">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Numéro</th>
                <th>Client</th>
                <th>Produit</th>
                <th>Devis</th>
                <th>État</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="repair in recentRepairs" :key="repair.id">
                <td><strong>{{ repair.invoice_number }}</strong></td>
                <td>{{ repair.client_name }}</td>
                <td>{{ productLabel(repair.product_category) }}</td>
                <td>{{ formatMoney(repair.repair_quote) }}</td>
                <td>
                  <span class="dash-status-pill" :class="{ inactive: repair.repair_status !== 'Active' }">
                    {{ repair.repair_status === 'Active' ? 'Active' : 'Inactif' }}
                  </span>
                </td>
                <td>{{ formatDate(repair.created_at) }}</td>
              </tr>
            </tbody>
          </table>

          <p v-if="recentRepairs.length === 0" class="dash-empty">
            Aucune réparation pour le moment.
          </p>
        </div>
      </div>
    </div>
  
</template>

<style>
@import '../../css/dashboard.css';

</style>
