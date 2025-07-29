<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
</script>

<template>
    <div>
        <!--        <div class="aspect-square items-center justify-center size-10">-->
        <!--            <AppLogoIcon class="rounded-lg fill-current text-white size-full dark:text-black border border-secondary" />-->
        <!--        </div>-->
        <div class="ml-1 flex-1 text-left text-sm text-primary">
            <span class="truncate font-semibold leading-tight tracking-tight font-serif text-2xl mb-0.5">
                {{ page.props.app.name }}
            </span>
        </div>
    </div>
</template>
