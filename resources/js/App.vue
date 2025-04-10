<script setup>
import {ref, onMounted} from 'vue'
import DataTable from './components/DataTable.vue'

const leads = ref([])
const total = ref(0)
const page = ref(1)
const limit = ref(25) // Теперь это реактивная переменная
const loading = ref(false)
const hasMore = ref(true) // Флаг для проверки наличия дополнительных данных

const fetchLeads = async () => {
    loading.value = true
    try {
        const response = await fetch(`/api/leads?page=${page.value}&limit=${limit.value}`)
        const data = await response.json()

        if (data.data.length === 0) {
            hasMore.value = false
            // Если это не первая страница, просто останавливаем загрузку
            if (page.value > 1) {
                page.value-- // Возвращаемся на предыдущую страницу
                return
            }
        } else {
            leads.value = data.data
            // Если данных меньше, чем запрошено, значит это последняя страница
            hasMore.value = data.data.length >= limit.value
        }

        // Если сервер не возвращает общее количество, можно установить большое число
        // или использовать специальное значение для бесконечной пагинации
        total.value = data.meta?.total ?? 1000000 // Большое число для имитации бесконечного списка
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

function onItemsPerPageChange(newLimit) {
    console.log('Изменение количества строк на странице:', newLimit)
    limit.value = newLimit
    page.value = 1 // Сбрасываем на первую страницу при изменении limit
    fetchLeads()
}
</script>

<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Лиды из AmoCRM</h1>
        <DataTable
            :leads="leads"
            :total="hasMore ? total : leads.length + (page-1) * limit"
            :current-page="page"
            :items-per-page="limit"
            :loading="loading"
            @update:current-page="onPageChange"
            @update:items-per-page="onItemsPerPageChange"
        />
    </div>
</template>
