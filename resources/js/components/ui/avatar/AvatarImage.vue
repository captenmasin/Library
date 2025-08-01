<script setup lang="ts">
import { useAttrs } from 'vue'
import { cn } from '@/lib/utils'
import { AvatarImage } from 'reka-ui'
// eslint-disable-next-line no-duplicate-imports
import type { AvatarImageProps } from 'reka-ui'

const props = defineProps<AvatarImageProps>()

const attrs = useAttrs()
</script>

<template>
    <AvatarImage
        data-slot="avatar-image"
        v-bind="props"
        :class="cn('aspect-square size-full', attrs.class || '')"
    >
        <slot />
    </AvatarImage>
</template>
