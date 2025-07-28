<script setup lang="ts">
import 'vue-sonner/style.css'
import MetaHead from '@/components/MetaHead.vue'
import AppHeaderLayout from '@/layouts/app/AppHeaderLayout.vue'
import { toast } from 'vue-sonner'
import { onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
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
</script>

<template>
    <AppHeaderLayout>
        <MetaHead />
        <slot />
        <Toaster
            :duration="2000"
            class="pointer-events-auto" />
    </AppHeaderLayout>
</template>
