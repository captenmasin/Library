<script setup lang="ts">
import 'vue-sonner/style.css'
import MetaHead from '@/components/MetaHead.vue'
import AppLayout from '@/layouts/app/AppHeaderLayout.vue'
import { toast } from 'vue-sonner'
import { watch, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Toaster } from '@/components/ui/sonner'
import type { BreadcrumbItemType } from '@/types'

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => []
})

const page = usePage()

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            toast.success(flash.success)
        }
        if (flash?.error) {
            toast.error(flash.error)
        }
        if (flash?.warning) {
            toast.warning(flash.warning)
        }
        if (flash?.info) {
            toast.info(flash.info)
        }
    },
    { immediate: true }
)

// if (document.startViewTransition) {
//     function handleInertiaStart () {
//         document.startViewTransition(async () => {
//             return new Promise((resolve) => {
//                 document.addEventListener(
//                     'inertia:finish',
//                     () => {
//                         resolve()
//                     },
//                     { once: true }
//                 )
//             })
//         })
//     }
//     document.addEventListener('inertia:start', handleInertiaStart)
//     onUnmounted(() => {
//         document.removeEventListener('inertia:start', handleInertiaStart)
//     })
// }
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <MetaHead />
        <slot />
        <Toaster class="pointer-events-auto" />
    </AppLayout>
</template>
