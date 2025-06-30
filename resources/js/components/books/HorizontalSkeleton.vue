<script setup lang="ts">

import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
    <div class="flex items-center gap-4 py-2">
        <Skeleton class="aspect-book w-22 shrink-0 rounded-sm overflow-hidden" />
        <div class="flex flex-col gap-1">
            <Skeleton class="h-5 w-48 rounded" />
            <Skeleton class="h-3 w-36 rounded mt-1" />
            <Skeleton class="h-3 w-80 rounded mt-2" />
            <Skeleton class="h-3 w-56 rounded" />
        </div>
        <Skeleton class="ml-auto flex h-8 w-44 shrink-0 justify-end items-center gap-2" />
    </div>
</template>
