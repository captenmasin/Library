<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'

const page = usePage()
const name = page.props.name
const quote = page.props.quote

defineProps<{
    title?: string;
    description?: string;
}>()
</script>

<template>
    <div class="relative grid flex-col items-center justify-center px-8 h-dvh sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
        <div class="relative hidden h-full flex-col p-10 text-white bg-muted dark:border-r lg:flex">
            <div class="absolute inset-0 bg-zinc-900" />
            <Link
                :href="useRoute('library.index')"
                class="relative z-20 flex items-center text-lg font-medium">
                <AppLogoIcon class="mr-2 fill-current text-white size-8" />
                {{ name }}
            </Link>
            <div
                v-if="quote"
                class="relative z-20 mt-auto">
                <blockquote class="space-y-2">
                    <p class="text-lg">
                        &ldquo;{{ quote.message }}&rdquo;
                    </p>
                    <footer class="text-sm text-neutral-300">
                        {{ quote.author }}
                    </footer>
                </blockquote>
            </div>
        </div>
        <div class="lg:p-8">
            <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">
                <div class="flex flex-col text-center space-y-2">
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
