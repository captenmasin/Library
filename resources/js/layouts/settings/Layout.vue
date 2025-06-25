<script setup lang="ts">
import Heading from '@/components/Heading.vue'
import { type NavItem } from '@/types'
import { Button } from '@/components/ui/button'
import { Link, usePage } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { Separator } from '@/components/ui/separator'

const sidebarNavItems: NavItem[] = [
    {
        title: 'Profile',
        href: useRoute('user.settings.profile.edit')
    },
    {
        title: 'Password',
        href: useRoute('user.settings.password.edit')
    },
    {
        title: 'Appearance',
        href: useRoute('user.settings.appearance')
    },
    {
        title: 'Danger zone',
        href: useRoute('user.settings.profile.danger')
    }
]

const page = usePage()

const currentPath = page.props.currentUrl
</script>

<template>
    <div class="px-4 py-6">
        <Heading
            title="Settings"
            description="Manage your profile and account settings" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-accent': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <slot />
                </section>
            </div>
        </div>
    </div>
</template>
