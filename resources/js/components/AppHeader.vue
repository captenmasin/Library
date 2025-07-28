<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import UserMenuContent from '@/components/UserMenuContent.vue'
import { computed, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import type { BreadcrumbItem, NavItem } from '@/types'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useAuthedUser } from '@/composables/useAuthedUser'
import { useIsCurrentUrl } from '@/composables/useIsCurrentUrl'
import { Home, LibraryBig, Menu, SearchIcon, PlusSquareIcon } from 'lucide-vue-next'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
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

const page = usePage()

const { authed, authedUser } = useAuthedUser()

const mobileMenuOpen = ref(false)

const activeItemStyles = computed(
    () => (url: string) => (useIsCurrentUrl(url) ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' : '')
)

const rightNavItems: NavItem[] = []

router.on('navigate', (event) => {
    mobileMenuOpen.value = false
})
</script>

<template>
    <div class="sticky md:static top-0 bg-background z-50">
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-12 md:h-16 justify-center items-center px-4 md:max-w-7xl">
                <Link
                    :href="useRoute('home')"
                    prefetch
                    class="flex items-center gap-x-2">
                    <span class="sr-only">
                        Go to Home
                    </span>
                    <div class="flex aspect-square items-center justify-center size-9 md:size-8">
                        <AppLogoIcon class="rounded-lg fill-current text-white size-full dark:text-black" />
                    </div>
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <NavigationMenuItem
                                v-for="(item, index) in navItems"
                                :key="index"
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
                                    class="absolute bottom-0 left-0 w-full translate-y-px bg-black h-0.5 dark:bg-white"
                                />
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="md:ml-auto absolute top-1/2 -translate-y-1/2 md:static md:translate-0 right-4 flex items-center space-x-2">
                    <div class="relative flex items-center space-x-1">
                        <div class="hidden space-x-1 lg:flex">
                            <template
                                v-for="item in rightNavItems"
                                :key="item.title">
                                <TooltipProvider :delay-duration="0">
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                                class="h-9 w-9 cursor-pointer group">
                                                <a
                                                    :href="item.href"
                                                    target="_blank"
                                                    rel="noopener noreferrer">
                                                    <span class="sr-only">{{ item.title }}</span>
                                                    <component
                                                        :is="item.icon"
                                                        class="opacity-80 size-5 group-hover:opacity-100" />
                                                </a>
                                            </Button>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>{{ item.title }}</p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </template>
                        </div>
                    </div>

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
