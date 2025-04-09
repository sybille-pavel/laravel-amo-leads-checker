<template>
    <EasyDataTable
        :headers="headers"
        :items="leads"
        :sort-by="'updated_at'"
        table-class="custom-table"
    />
</template>

<script setup>
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'

defineProps({
    leads: Array
})

const headers = [
    { text: 'Название лида', value: 'name', sortable: true },
    { text: 'Статус лида', value: 'status', sortable: true },
    { text: 'Контактные данные', value: 'contact', sortable: true },
    { text: 'Дата последнего изменения', value: 'updated_at', sortable: true }
]

</script>

<style scoped>
.custom-table {
    --easy-table-border: 1px solid #ccc;
}
</style>
