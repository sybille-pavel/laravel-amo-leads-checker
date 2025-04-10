<script setup>
import { ref, onMounted } from 'vue'
import DataTable from './components/DataTable.vue'

const leads = ref([])
const total = ref(0)
const page = ref(1)
const limit = 25
const loading = ref(false)

const fetchLeads = async () => {
    loading.value = true
    try {
        const response = await fetch(`/api/leads?page=${page.value}&limit=${limit}`)
        const data = await response.json()
        leads.value = data.data
        total.value = data.meta.total
    } catch (e) {
        console.error('Ошибка загрузки:', e)
    } finally {
        loading.value = false
    }
}

onMounted(fetchLeads)

function onPageChange(newPage) {
    console.log('Смена страницы на:', newPage)
    page.value = newPage
    fetchLeads()
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Лиды из AmoCRM</h1>
        <DataTable
        :leads="leads"
        :total="total"
        :current-page="page"
        :items-per-page="limit"
        :loading="loading"
        @update:current-page="onPageChange"
    />
    </div>
</template>
