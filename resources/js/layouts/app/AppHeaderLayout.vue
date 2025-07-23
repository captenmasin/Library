<script setup lang="ts">
import AppShell from '@/components/AppShell.vue'
import AppHeader from '@/components/AppHeader.vue'
import AppContent from '@/components/AppContent.vue'
import { ref } from 'vue'
import type { BreadcrumbItemType } from '@/types'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const breadcrumbs = ref(page.props.breadcrumbs as BreadcrumbItemType[] | undefined)

router.on('navigate', (event) => {
    const newBreadcrumbs = event.detail.page.props.breadcrumbs as BreadcrumbItemType[] | undefined
    if (newBreadcrumbs) {
        breadcrumbs.value = newBreadcrumbs
    }
})
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader :breadcrumbs="breadcrumbs" />
        <AppContent class="mt-4">
            <slot />
        </AppContent>
    </AppShell>
</template>
