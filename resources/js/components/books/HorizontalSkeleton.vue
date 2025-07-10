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
            <Skeleton class="shrink-0 overflow-hidden rounded-sm aspect-book w-20 md:w-22" />
            <div class="flex flex-col gap-1 w-full">
                <Skeleton class="h-5 w-full md:w-48 rounded" />
                <Skeleton class="md:mt-1 h-3 w-36 rounded" />
                <Skeleton class="mt-1 md:mt-2 h-3 w-full md:w-80 rounded" />
                <Skeleton class="h-3 w-full md:w-56 rounded" />
            </div>
        </div>
        <div
            v-if="withActions"
            class="md:ml-auto -mt-11 pl-24 md:pl-0 max-w-64 md:max-w-none shrink-0 w-full md:w-40 flex">
            <Skeleton class="h-8 w-full" />
        </div>
    </div>
</template>
