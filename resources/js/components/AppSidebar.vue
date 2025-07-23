<script setup lang="ts">
import AppLogo from './AppLogo.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import NavFooter from '@/components/NavFooter.vue'
import { type NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'
import { LayoutGrid } from 'lucide-vue-next'
import { useRoute } from '@/composables/useRoute'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'

const mainNavItems: NavItem[] = [
    {
        title: 'Books',
        href: useRoute('user.books.index'),
        icon: LayoutGrid
    }
]

const footerNavItems: NavItem[] = [

]
</script>

<template>
    <Sidebar
        collapsible="icon"
        variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton
                        size="lg"
                        as-child>
                        <Link :href="useRoute('user.books.index')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
