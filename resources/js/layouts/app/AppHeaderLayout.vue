<script setup lang="ts">
import AppShell from '@/components/AppShell.vue'
import AppHeader from '@/components/AppHeader.vue'
import AppContent from '@/components/AppContent.vue'
import { computed, onMounted, ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { Link, router, usePage } from '@inertiajs/vue3'
import type { BreadcrumbItemType, NavItem } from '@/types'
import { useIsCurrentUrl } from '@/composables/useIsCurrentUrl'
import { Home, LibraryBig, PlusSquareIcon } from 'lucide-vue-next'

const page = usePage()
const breadcrumbs = ref(page.props.breadcrumbs as BreadcrumbItemType[] | undefined)

const mainNavItems = ref([
    {
        title: 'Home',
        href: useRoute('home'),
        icon: Home,
        isActive: false
    },
    {
        title: 'Library',
        href: useRoute('user.books.index'),
        icon: LibraryBig,
        isActive: false
    },
    {
        title: 'Add Books',
        href: useRoute('books.search'),
        icon: PlusSquareIcon,
        isActive: false
    }
])

const activeItemStyles = computed(
    () => (item: NavItem) => (item.isActive ? 'text-primary bg-primary/10' : '')
)

onMounted(() => {
    // Set active state for main navigation items based on current URL
    mainNavItems.value.forEach(item => {
        item.isActive = useIsCurrentUrl(item.href)
    })
})

function handleClick (item: NavItem) {
    // Update the active state when a navigation item is clicked
    mainNavItems.value.forEach(navItem => {
        navItem.isActive = (navItem.href === item.href)
    })
}

router.on('navigate', (event) => {
    const newBreadcrumbs = event.detail.page.props.breadcrumbs as BreadcrumbItemType[] | undefined
    if (newBreadcrumbs) {
        breadcrumbs.value = newBreadcrumbs
    }
})
</script>

<template>
    <AppShell class="flex-col">
        <AppHeader
            :nav-items="mainNavItems"
            :breadcrumbs="breadcrumbs" />
        <AppContent class="mt-4">
            <slot />
        </AppContent>
        <div
            style="padding-bottom: env(safe-area-inset-bottom)"
            class="sticky bg-background/75 border-t border-background-foreground backdrop-blur-sm px-1 bottom-0 left-0 right-0 z-50">
            <ul class="flex items-center w-full pt-1 pb-2">
                <li
                    v-for="item in mainNavItems"
                    :key="item.title"
                    class="flex flex-1">
                    <Link
                        :href="item.href"
                        prefetch
                        :class="[activeItemStyles(item)]"
                        class="flex py-3 w-full rounded-xl items-center justify-center text-sm text-foreground hover:text-primary"
                        @click="handleClick(item)">
                        <component
                            :is="item.icon" />
                    </Link>
                </li>
            </ul>
        </div>
    </AppShell>
</template>
