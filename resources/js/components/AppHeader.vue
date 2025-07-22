<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
import AppLogoIcon from '@/components/AppLogoIcon.vue'
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import UserMenuContent from '@/components/UserMenuContent.vue'
import { computed, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import type { BreadcrumbItem, NavItem } from '@/types'
import { getInitials } from '@/composables/useInitials'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useAuthedUser } from '@/composables/useAuthedUser'
import { useImageTransform } from '@/composables/useImageTransform'
import { Home, LibraryBig, Menu, SearchIcon } from 'lucide-vue-next'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { NavigationMenu, NavigationMenuItem, NavigationMenuList, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu'

interface Props {
    breadcrumbs?: BreadcrumbItem[];
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => []
})

const { getImageUrl } = useImageTransform()

const page = usePage()

const { authed, authedUser } = useAuthedUser()

const mobileMenuOpen = ref(false)

const isCurrentRoute = computed(() => (url: string) => {
    let cleanedUrl = url.replace(page.props.app.url, '')
    if (cleanedUrl === '') {
        cleanedUrl = '/'
    }

    // remove parameters from the URL
    const cleanedPageUrl = page.url.split('?')[0]

    return cleanedPageUrl === cleanedUrl
})

const activeItemStyles = computed(
    () => (url: string) => (isCurrentRoute.value(url) ? 'text-neutral-900 dark:bg-neutral-800 dark:text-neutral-100' : '')
)

const mainNavItems: NavItem[] = [
    {
        title: 'Home',
        href: useRoute('home'),
        icon: Home
    },
    {
        title: 'Library',
        href: useRoute('user.books.index'),
        icon: LibraryBig
    },
    {
        title: 'Find Books',
        href: useRoute('books.search'),
        icon: SearchIcon
    }
]

const rightNavItems: NavItem[] = []

router.on('navigate', (event) => {
    mobileMenuOpen.value = false
})
</script>

<template>
    <div>
        <div class="border-b border-sidebar-border/80">
            <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl">
                <!-- Mobile Menu -->
                <div class="lg:hidden">
                    <Sheet v-model:open="mobileMenuOpen">
                        <SheetTrigger :as-child="true">
                            <Button
                                variant="ghost"
                                size="icon"
                                class="mr-2 h-9 w-9">
                                <Menu class="h-5 w-5" />
                            </Button>
                        </SheetTrigger>
                        <SheetContent
                            side="left"
                            class="p-6 w-[300px]">
                            <SheetTitle class="sr-only">
                                Navigation Menu
                            </SheetTitle>
                            <SheetHeader class="flex justify-start text-left">
                                <AppLogoIcon class="rounded-sm fill-current text-black size-6 dark:text-white" />
                            </SheetHeader>
                            <div class="flex h-full flex-1 flex-col justify-between py-6 space-y-4">
                                <nav class="-mx-3 space-y-1">
                                    <Link
                                        v-for="item in mainNavItems"
                                        :key="item.title"
                                        prefetch
                                        :href="item.href"
                                        class="flex items-center gap-x-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-accent"
                                        :class="activeItemStyles(item.href)"
                                    >
                                        <component
                                            :is="item.icon"
                                            v-if="item.icon"
                                            class="h-5 w-5" />
                                        {{ item.title }}
                                    </Link>
                                </nav>
                                <div class="flex flex-col space-y-4">
                                    <a
                                        v-for="item in rightNavItems"
                                        :key="item.title"
                                        :href="item.href"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center text-sm font-medium space-x-2"
                                    >
                                        <component
                                            :is="item.icon"
                                            v-if="item.icon"
                                            class="h-5 w-5" />
                                        <span>{{ item.title }}</span>
                                    </a>
                                </div>
                            </div>
                        </SheetContent>
                    </Sheet>
                </div>

                <Link
                    :href="useRoute('home')"
                    prefetch
                    class="flex items-center gap-x-2">
                    <span class="sr-only">
                        Go to Home
                    </span>
                    <div class="flex aspect-square items-center justify-center size-8">
                        <AppLogoIcon class="rounded-lg fill-current text-white size-full dark:text-black" />
                    </div>
                </Link>

                <!-- Desktop Menu -->
                <div class="hidden h-full lg:flex lg:flex-1">
                    <NavigationMenu class="ml-10 flex h-full items-stretch">
                        <NavigationMenuList class="flex h-full items-stretch space-x-2">
                            <NavigationMenuItem
                                v-for="(item, index) in mainNavItems"
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
                                    v-if="isCurrentRoute(item.href)"
                                    class="absolute bottom-0 left-0 w-full translate-y-px bg-black h-0.5 dark:bg-white"
                                />
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <div class="ml-auto flex items-center space-x-2">
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

        <div
            v-if="props.breadcrumbs.length > 1"
            class="flex w-full border-b border-sidebar-border/70">
            <div class="mx-auto flex h-12 w-full items-center justify-start px-4 text-neutral-500 md:max-w-7xl">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>
    </div>
</template>
