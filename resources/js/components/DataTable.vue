<template>
    <EasyDataTable
        :headers="headers"
        :items="leads"
        :server-options="serverOptions"
        :server-items-length="total"
        :loading="loading"
        @update:server-options="onServerOptionsUpdate"
    />
</template>

<script setup>
import { computed } from 'vue'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'

const props = defineProps({
    leads: Array,
    total: Number,
    currentPage: Number,
    itemsPerPage: Number,
    loading: Boolean
})
const emit = defineEmits(['update:current-page'])

const headers = [
    {text: 'Название', value: 'name'},
    {text: 'Статус', value: 'status'},
    {text: 'Контакт', value: 'contact'},
    {text: 'Обновлено', value: 'updated_at'}
]

// Собираем serverOptions из props
const serverOptions = computed(() => ({
    page: props.currentPage,
    rowsPerPage: props.itemsPerPage,
}))

function onServerOptionsUpdate(newOptions) {
    emit('update:current-page', newOptions.page)
}
</script>
