<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import BookSearchForm from '@/components/books/BookSearchForm.vue'

import { Head } from '@inertiajs/vue3'

defineProps({
    query: String
})

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <BookSearchForm :query="query" />
</template>
