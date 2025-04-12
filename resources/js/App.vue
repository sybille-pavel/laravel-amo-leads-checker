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

const selectedStatus = ref(0)
const statuses = ref({
    0: 'Все'
})

let pipelines = [];

const fetchLeads = async () => {
    loading.value = true
    try {
        const url = new URL('/api/leads', window.location.origin)
        url.searchParams.set('page', page.value)
        url.searchParams.set('limit', limit.value)
        url.searchParams.set('sortBy', sortBy.value)
        url.searchParams.set('sortDirection', sortType.value)
        url.searchParams.set('status', selectedStatus.value)
        url.searchParams.set('pipelines', getPipelines(selectedStatus.value))

        const response = await fetch(url)
        const data = await response.json()

        leads.value = data.data
        hasMore.value = data.data.length >= limit.value
        total.value = data.meta?.total ?? 1000000
    } catch (e) {
        loading.value = false
        leads.value = []
        if(page.value > 0){
            page.value = 0;
        }
        alert(e);
    } finally {
        loading.value = false
    }
}


const fetchStatuses = async () => {
    try {
        const url = new URL('/api/statuses', window.location.origin);
        const response = await fetch(url);
        const data = await response.json();
        console.log(data);

        const newStatuses = { ...statuses.value };

        for (const innerObj of Object.values(data.data)) {
            for (const [id, name] of Object.entries(innerObj)) {
                newStatuses[id] = name;
            }
        }

        pipelines = data.data;
        statuses.value = newStatuses;
        console.log(statuses.value);
    } catch (e) {
        console.error('Ошибка получения статусов:', e);
    }
};

watch([page, limit, sortBy, sortType, selectedStatus], fetchLeads, {immediate: true})

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

function getPipelines(input){
    for (let pipelineKey in pipelines) {
        const pipeline = pipelines[pipelineKey];

        if (pipeline[input]) {
            return pipelineKey;
        }
    }

    return null;  // Если не найдено
}

onMounted(() => {
    fetchStatuses()
})

</script>

<template>
    <div class="p-6 space-y-4">
        <h1 class="text-2xl font-bold">Лиды из AmoCRM</h1>

        <div class="flex items-center gap-4">
            <label class="">Фильтр по статусу:</label>
            <select v-model="selectedStatus" class="bg-gray-800 text-white px-3 py-1 rounded">
                <option
                    v-for="(name, id) in statuses"
                    :key="id"
                    :value="id"
                >
                    {{ name }}
                </option>
            </select>
        </div>

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

