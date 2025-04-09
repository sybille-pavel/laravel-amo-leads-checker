<script setup>
import { ref, onMounted } from 'vue'
import DataTable from './components/DataTable.vue'

const leads = ref([])
const loading = ref(true)

onMounted(async () => {
    try {
        const response = await fetch('/api/leads')
        const data = await response.json()
        console.log(data);
        leads.value = data
    } catch (e) {
        alert('Ошибка загрузки лидов:' + e)
        console.error('Ошибка загрузки лидов:', e)
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Лиды из AmoCRM</h1>
        <div v-if="loading">Загрузка...</div>
        <DataTable v-else :leads="leads" />
    </div>
</template>
