<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
    <div>
        <div class="aspect-square items-center justify-center size-8 md:size-8">
            <AppLogoIcon class="rounded-lg fill-current text-white size-full dark:text-black" />
        </div>
        <div class="ml-1 grid flex-1 text-left text-sm text-primary">
            <span class="truncate font-semibold leading-tight tracking-tight font-serif text-lg mb-0.5">
                {{ page.props.app.name }}
            </span>
        </div>
    </div>
</template>
