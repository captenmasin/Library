<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import AppLogo from '@/components/AppLogo.vue'
import UserAvatar from '@/components/UserAvatar.vue'
import UserMenuContent from '@/components/UserMenuContent.vue'
import { Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import type { BreadcrumbItem, NavItem } from '@/types'
import { useAuthedUser } from '@/composables/useAuthedUser'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import { useIsCurrentUrl } from '@/composables/useIsCurrentUrl'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { NavigationMenu, NavigationMenuItem, NavigationMenuList, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu'

interface Props {
    breadcrumbs?: BreadcrumbItem[];
    navItems?: NavItem[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    navItems: () => []
})

const { authed, authedUser } = useAuthedUser()

const mobileMenuOpen = ref(false)

const activeItemStyles = computed(
    () => (url: string) => (useIsCurrentUrl(url) ? 'text-primary hover:text-primary dark:bg-neutral-800 dark:text-neutral-100' : '')
)

const isVisible = ref(true)
let lastScroll = window.scrollY

const handleScroll = () => {
    const currentScroll = window.scrollY

    if (currentScroll > lastScroll && currentScroll > 50) {
        isVisible.value = false // scrolling down
    } else if (currentScroll < lastScroll) {
        isVisible.value = true // scrolling up
    }

    lastScroll = currentScroll
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll, { passive: true })
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
})

router.on('navigate', (event) => {
    mobileMenuOpen.value = false
})
</script>

<template>
    <div
        class="sticky md:static top-0 md:translate-y-0 bg-background z-50 transition-all duration-300 ease-in-out"
        :class="{ '-translate-y-full': !isVisible }">
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-14 md:h-16 items-center px-4 md:max-w-7xl pwa:md:max-w-none">
                <div
                    :class="$page.props.backUrl ? 'ml-0 opacity-100' : '-ml-8 opacity-0'"
                    class="md:hidden mr-2 transition-all duration-300">
                    <Link
                        tabindex="-1"
                        class="flex -ml-4 pl-2 text-primary"
                        :href="$page.props.backUrl ?? useRoute('home')">
                        <Icon
                            name="ChevronLeft"
                            class="size-8 stroke-[1.5px]" />
                    </Link>
                </div>

                <Link
                    :href="useRoute('home')"
                    prefetch
                    class="flex items-center gap-x-2">
                    <span class="sr-only">
                        Go to Home
                    </span>
                    <AppLogo class="flex items-center" />
                    <!--                    <div class="hidden lg:flex aspect-square items-center justify-center size-9 md:size-12">-->
                    <!--                        <AppLogoIcon class="rounded-lg fill-current text-white size-full dark:text-black" />-->
                    <!--                    </div>-->
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <NavigationMenuItem
                                v-for="(item, index) in navItems"
                                :key="index"
                                :class="item.mobileOnly ? 'flex lg:hidden' : 'flex lg:flex-1'"
                                class="relative flex h-full items-center">
                                <Link
                                    prefetch
                                    :class="[navigationMenuTriggerStyle(), activeItemStyles(item.href), 'h-9 cursor-pointer px-3']"
                                    :href="item.href"
                                >
                                    <component
                                        :is="item.icon"
                                        v-if="item.icon"
                                        class="mr-2 h-4 w-4" />
                                    {{ item.title }}
                                </Link>
                                <div
                                    v-if="useIsCurrentUrl(item.href)"
                                    class="absolute bottom-0 left-0 w-full translate-y-px bg-primary h-0.5 dark:bg-white"
                                />
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="md:ml-auto absolute top-1/2 -translate-y-1/2 md:static md:translate-0 right-4 flex items-center space-x-2">
                    <DropdownMenu v-if="authed && authedUser">
                        <DropdownMenuTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="relative w-auto rounded-full p-1 size-10 focus-within:ring-primary focus-within:ring-2"
                            >
                                <UserAvatar :user="authedUser" />
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            align="end"
                            class="w-56">
                            <UserMenuContent :user="authedUser" />
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
            </div>
        </div>

        <!--        <div-->
        <!--            v-if="props.breadcrumbs.length > 1"-->
        <!--            class="flex w-full border-b border-sidebar-border/70">-->
        <!--            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">-->
        <!--                <Breadcrumbs :breadcrumbs="breadcrumbs" />-->
        <!--            </div>-->
        <!--        </div>-->
    </div>
</template>
