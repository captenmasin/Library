<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'

defineProps({
    withActions: {
        type: Boolean,
        default: true
    }
})
</script>

<template>
    <div class="flex flex-col md:flex-row w-full md:items-center gap-2 md:gap-4 py-2">
        <div class="flex gap-4">
            <Skeleton class="shrink-0 overflow-hidden rounded-sm aspect-book w-18 md:w-22" />
            <div class="flex flex-col gap-1">
                <Skeleton class="h-5 w-48 rounded" />
                <Skeleton class="mt-1 h-3 w-36 rounded" />
                <Skeleton class="mt-2 h-3 w-80 rounded" />
                <Skeleton class="h-3 w-56 rounded" />
            </div>
        </div>
        <Skeleton
            v-if="withActions"
            class="ml-auto flex h-8 w-full md:w-44 shrink-0 items-center justify-end gap-2" />
    </div>
</template>
