<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { Button } from '@/components/ui/button/index.js'

const props = defineProps({
    status: Number
})

const title = computed(() => {
    return {
        503: 'Under maintenance',
        500: 'Something went wrong',
        404: 'Oops!',
        403: 'Oops!'
    }[props.status]
})

const message = computed(() => {
    return {
        503: 'We\'re running some routine maintenance. We\'ll be right back!',
        500: 'Try refreshing, or contact us if the issue persists.',
        404: 'We couldn\'t find the page you\'re looking for.',
        403: 'We couldn\'t find the page you\'re looking for.'
    }[props.status]
})
</script>

<template>
    <AppLayout>
        <div class="mx-auto w-full max-w-6xl px-6 py-24 lg:px-8">
            <div class="flex items-center">
                <div class="w-1/2">
                    <p class="text-base font-semibold leading-8 text-primary dark:text-white">
                        {{ status }}
                    </p>
                    <h1 class="text-4xl font-semibold font-heading line-height-1 sm:text-[4rem] md:mt-2">
                        {{ title }}
                    </h1>
                    <p class="mt-1 max-w-xs text-base text-zinc-600 text-pretty dark:text-zinc-400 sm:text-xl md:mt-6">
                        {{ message }}
                    </p>
                    <div
                        v-if="[404, 403].includes(status)"
                        class="mt-4 flex">
                        <Button
                            as-child>
                            <Link :href="useRoute('home')">
                                Go home
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
