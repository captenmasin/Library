<script setup lang="ts">
import { cn } from '@/lib/utils'
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import type { ComboboxAnchorProps } from 'reka-ui'
import { ComboboxAnchor, useForwardProps } from 'reka-ui'

const props = defineProps<ComboboxAnchorProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = reactiveOmit(props, 'class')

const forwarded = useForwardProps(delegatedProps)
</script>

<template>
    <ComboboxAnchor
        data-slot="combobox-anchor"
        v-bind="forwarded"
        :class="cn('w-[200px]', props.class)"
    >
        <slot />
    </ComboboxAnchor>
</template>
