<script setup lang="ts">
import { cn } from '@/lib/utils'
import { computed, type HTMLAttributes } from 'vue'
import { DialogOverlay, type DialogOverlayProps } from 'reka-ui'

const props = defineProps<DialogOverlayProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
    const { class: _, ...delegated } = props

    return delegated
})
</script>

<template>
    <DialogOverlay
        data-slot="sheet-overlay"
        :class="cn('data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 backdrop-blur-sm data-[state=open]:fade-in-0 fixed inset-0 z-50 bg-black/80', props.class)"
        v-bind="delegatedProps"
    >
        <slot />
    </DialogOverlay>
</template>
