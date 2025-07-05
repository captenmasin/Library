<script setup lang="ts">
import Icon from '@/components/Icon.vue'
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
    <div
        class="mt-12 flex items-center justify-center rounded-lg border-2 border-dashed py-16 px-4 gap-2 flex-col text-sm text-center text-muted-foreground border-primary/10">
        <Icon
            name="BookDown"
            class="size-8 animate-bounce" />
        <h3 class="font-semibold text-2xl font-serif">
            Importing...
        </h3>
        <p>
            Please wait while we import the book. This may take a few minutes depending on the size of the book.
        </p>
    </div>
</template>
