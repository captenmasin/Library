<script setup lang="ts">
import { cn } from '@/lib/utils'
import { X } from 'lucide-vue-next'
import type { HTMLAttributes } from 'vue'
import { reactiveOmit } from '@vueuse/core'
import { TagsInputItemDelete, type TagsInputItemDeleteProps, useForwardProps } from 'reka-ui'

const props = defineProps<TagsInputItemDeleteProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = reactiveOmit(props, 'class')

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
    <TagsInputItemDelete
        v-bind="forwardedProps"
        :class="cn('flex rounded bg-transparent mr-1', props.class)">
        <slot>
            <X class="h-4 w-4" />
        </slot>
    </TagsInputItemDelete>
</template>
