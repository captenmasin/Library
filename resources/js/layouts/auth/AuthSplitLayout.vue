<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'

const page = usePage()
const name = page.props.name

defineProps<{
    title?: string;
    description?: string;
}>()
</script>

<template>
    <div class="relative grid flex-col items-center bg-background justify-center px-8 h-dvh sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col p-10 text-white dark:border-r lg:flex">
            <div class="absolute inset-4 bg-secondary text-secondary-foreground rounded-xl" />
            <Link
                :href="useRoute('user.books.index')"
                class="relative z-20 flex items-center text-xl font-serif text-foreground font-medium">
                <AppLogoIcon class="mr-2 rounded-lg fill-current size-8" />
                {{ name }}
            </Link>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-sm">
                <AppLogoIcon class="rounded-lg fill-current size-10 mx-auto lg:hidden mb-2" />
                <div class="flex flex-col text-center space-y-1">
                    <h1
                        v-if="title"
                        class="text-xl font-medium tracking-tight">
                        {{ title }}
                    </h1>
                    <p
                        v-if="description"
                        class="text-sm text-muted-foreground">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
