<script setup lang="ts">
import { cn } from '@/lib/utils'
import { Primitive } from 'reka-ui'
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
// eslint-disable-next-line no-duplicate-imports
import type { PrimitiveProps } from 'reka-ui'
import { type BadgeVariants, badgeVariants } from '.'

const props = defineProps<PrimitiveProps & {
    variant?: BadgeVariants['variant']
    class?: HTMLAttributes['class']
}>()

const delegatedProps = reactiveOmit(props, 'class')
</script>

<template>
    <Primitive
        data-slot="badge"
        :class="cn(badgeVariants({ variant }), props.class)"
        v-bind="delegatedProps"
    >
        <slot />
    </Primitive>
</template>
