<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'
import { Input } from '@/components/ui/input'

const props = defineProps<{
    class?: HTMLAttributes['class']
}>()
</script>

<template>
    <Input
        data-slot="sidebar-input"
        data-sidebar="input"
        :class="cn(
            'bg-background h-8 w-full shadow-none',
            props.class,
        )"
    >
        <slot />
    </Input>
</template>
