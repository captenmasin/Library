<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
</script>

<template>
    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-xl border border-primary/20 size-8">
        <AppLogoIcon class="fill-current text-white size-full dark:text-black" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm text-primary">
        <span class="truncate font-semibold leading-tight mb-0.5">
            Library
        </span>
    </div>
</template>
