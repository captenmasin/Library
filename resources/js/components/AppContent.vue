<script setup lang="ts">
import { computed, ref } from 'vue'
import { SidebarInset } from '@/components/ui/sidebar'

interface Props {
    variant?: 'header' | 'sidebar';
    class?: string;
}

const props = defineProps<Props>()
const className = computed(() => props.class)
</script>

<template>
    <SidebarInset
        v-if="props.variant === 'sidebar'"
        :class="className">
        <slot />
    </SidebarInset>
    <main
        v-else
        class="mx-auto flex h-full w-full max-w-7xl pwa:max-w-none flex-1 flex-col gap-4 rounded-xl px-4"
        :class="className">
        <slot />
    </main>
</template>
