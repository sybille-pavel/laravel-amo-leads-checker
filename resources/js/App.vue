<script setup>
import {ref, onMounted, watch} from 'vue'
import DataTable from './components/DataTable.vue'

const leads = ref([])
const total = ref(0)
const page = ref(1)
const limit = ref(25)
const sortBy = ref('updated_at')
const sortType = ref('desc')
const loading = ref(false)
const hasMore = ref(true)

const fetchLeads = async () => {
    loading.value = true
    try {
        const url = new URL('/api/leads', window.location.origin)
        url.searchParams.set('page', page.value)
        url.searchParams.set('limit', limit.value)
        url.searchParams.set('sortBy', sortBy.value)
        url.searchParams.set('sortDirection', sortType.value)

        const response = await fetch(url)
        const data = await response.json()

        leads.value = data.data
        hasMore.value = data.data.length >= limit.value
        total.value = data.meta?.total ?? 1000000
    } catch (e) {
        console.error('Ошибка загрузки:', e)
    } finally {
        loading.value = false
    }
}

watch([page, limit, sortBy, sortType], fetchLeads, {immediate: true})

function onPageChange(newPage) {
    page.value = newPage
}

function onItemsPerPageChange(newLimit) {
    limit.value = newLimit
}

function onSortByChange(newSortBy) {
    console.log(newSortBy);
    sortBy.value = newSortBy
}

function onSortTypeChange(newSortType) {
    console.log(newSortType)
    sortType.value = newSortType
}
</script>

<template>
    <h1>{{page}}</h1>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Лиды из AmoCRM</h1>
        <DataTable
            :leads="leads"
            :total="hasMore ? total : leads.length + (page - 1) * limit"
            :current-page="page"
            :items-per-page="limit"
            :sort-by="sortBy"
            :sort-type="sortType"
            :loading="loading"
            @update:current-page="onPageChange"
            @update:items-per-page="onItemsPerPageChange"
            @update:sort-by="onSortByChange"
            @update:sort-type="onSortTypeChange"
        />
    </div>
</template>
