<script lang="ts" setup>
import { cn } from '@/lib/utils'
import { DrawerTitle } from 'vaul-vue'
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import type { DrawerTitleProps } from 'vaul-vue'

const props = defineProps<DrawerTitleProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = reactiveOmit(props, 'class')
</script>

<template>
    <DrawerTitle
        data-slot="drawer-title"
        v-bind="delegatedProps"
        :class="cn('text-foreground font-semibold', props.class)"
    >
        <slot />
    </DrawerTitle>
</template>
