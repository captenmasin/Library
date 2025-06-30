<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { router, usePoll } from '@inertiajs/vue3'

defineProps({
    identifier: {
        type: String,
        required: true
    }
})

const interval = setInterval(function () {
    router.reload()
}, 5000)

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold mb-4">
            Import Book
        </h1>
        <p class="mb-4">
            Identifier: {{ identifier }}
        </p>
    </div>
</template>
