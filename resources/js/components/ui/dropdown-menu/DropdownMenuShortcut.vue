<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'

const props = defineProps<{
    class?: HTMLAttributes['class']
}>()
</script>

<template>
    <span
        data-slot="dropdown-menu-shortcut"
        :class="cn('text-muted-foreground ml-auto text-xs tracking-widest', props.class)"
    >
        <slot />
    </span>
</template>
