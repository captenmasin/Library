<script setup lang="ts">
import { AvatarImage } from 'reka-ui'
// eslint-disable-next-line no-duplicate-imports
import type { AvatarImageProps } from 'reka-ui'

const props = defineProps<AvatarImageProps>()
</script>

<template>
    <AvatarImage
        data-slot="avatar-image"
        v-bind="props"
        class="aspect-square size-full"
    >
        <slot />
    </AvatarImage>
</template>
