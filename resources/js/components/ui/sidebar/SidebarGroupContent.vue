<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'

const props = defineProps<{
    class?: HTMLAttributes['class']
}>()
</script>

<template>
    <div
        data-slot="sidebar-group-content"
        data-sidebar="group-content"
        :class="cn('w-full text-sm', props.class)"
    >
        <slot />
    </div>
</template>
