<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import { ComboboxSeparator } from 'reka-ui'
import type { ComboboxSeparatorProps } from 'reka-ui'

const props = defineProps<ComboboxSeparatorProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = reactiveOmit(props, 'class')
</script>

<template>
    <ComboboxSeparator
        data-slot="combobox-separator"
        v-bind="delegatedProps"
        :class="cn('bg-border -mx-1 h-px', props.class)"
    >
        <slot />
    </ComboboxSeparator>
</template>
