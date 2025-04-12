<template>
    <EasyDataTable
        :headers="headers"
        :items="leads"
        :server-options="serverOptions"
        :server-items-length="total"
        :loading="loading"
        @update:server-options="onServerOptionsUpdate"
        :rows-items="[25, 50, 100]"
    rows-per-page-message="Строк на странице:"
    />
</template>

<script setup>
import {computed} from 'vue'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'

const props = defineProps({
    leads: Array,
    total: Number,
    currentPage: Number,
    itemsPerPage: Number,
    loading: Boolean,
    sortBy: String,
    sortType: String
})

const emit = defineEmits([
    'update:current-page',
    'update:items-per-page',
    'update:sort-by',
    'update:sort-type'
])

const headers = [
    {text: 'Название', value: 'name'},
    {text: 'Статус', value: 'status'},
    {text: 'Контакт', value: 'contact'},
    {text: 'Обновлено', value: 'updated_at', sortable: true},
    {text: 'Создано', value: 'created_at', sortable: true}
]

const serverOptions = computed(() => ({
    page: props.currentPage,
    rowsPerPage: props.itemsPerPage,
    sortBy: props.sortBy,
    sortType: props.sortType
}))

function onServerOptionsUpdate(newOptions) {
    if (newOptions.sortBy !== props.sortBy ||
        newOptions.sortType !== props.sortType) {
        console.log(newOptions)
        emit('update:sort-by', newOptions.sortBy)
        emit('update:sort-type', newOptions.sortType)
    } else if (newOptions.rowsPerPage !== props.itemsPerPage) {
        emit('update:items-per-page', newOptions.rowsPerPage)
    } else if (newOptions.page !== props.currentPage) {
        emit('update:current-page', newOptions.page)
    }
}
</script>

<style>
.pagination__items-index{
    visibility: hidden;
}
</style>
