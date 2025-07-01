<script setup lang="ts">
import { Monitor, Moon, Sun } from 'lucide-vue-next'
import { useAppearance } from '@/composables/useAppearance'

const { appearance, updateAppearance } = useAppearance()

const tabs = [
    { value: 'light', Icon: Sun, label: 'Light' },
    { value: 'dark', Icon: Moon, label: 'Dark' },
    { value: 'system', Icon: Monitor, label: 'System' }
] as const
</script>

<template>
    <div class="inline-flex gap-1 rounded-lg p-1 bg-muted dark:bg-neutral-800">
        <button
            v-for="{ value, Icon, label } in tabs"
            :key="value"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                appearance === value
                    ? 'bg-white text-foreground shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                    : 'text-foreground hover:bg-white/40 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
            @click="updateAppearance(value)"
        >
            <component
                :is="Icon"
                class="-ml-1 h-4 w-4" />
            <span class="text-sm ml-1.5">{{ label }}</span>
        </button>
    </div>
</template>
