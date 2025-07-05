<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { router } from '@inertiajs/vue3'

defineProps({
    identifier: {
        type: String,
        required: true
    }
})

setInterval(function () {
    router.reload()
}, 2000)

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <h1 class="mb-4 text-2xl font-bold">
            Import Book
        </h1>
        <p class="mb-4">
            Identifier: {{ identifier }}
        </p>
    </div>
</template>
