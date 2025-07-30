<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

defineProps({
    logoSize: {
        type: String,
        default: 'size-9'
    },
    textSize: {
        type: String,
        default: 'text-lg'
    }
})
</script>

<template>
    <div>
        <div
            class="aspect-square items-center justify-center"
            :class="logoSize">
            <AppLogoIcon class="rounded-lg fill-current text-primary border border-secondary size-full dark:text-white" />
        </div>
        <div class="ml-2 grid flex-1 text-left text-sm text-primary">
            <span
                class="truncate font-semibold leading-tight tracking-tight font-serif mb-0.5"
                :class="textSize">
                {{ page.props.app.name }}
            </span>
        </div>
    </div>
</template>
