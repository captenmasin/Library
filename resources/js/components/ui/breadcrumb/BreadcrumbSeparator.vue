<script lang="ts" setup>
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'
import { ChevronRight } from 'lucide-vue-next'

const props = defineProps<{
    class?: HTMLAttributes['class']
}>()
</script>

<template>
    <li
        data-slot="breadcrumb-separator"
        role="presentation"
        aria-hidden="true"
        :class="cn('[&>svg]:size-3.5', props.class)"
    >
        <slot>
            <ChevronRight />
        </slot>
    </li>
</template>
