<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue'
import type { User } from '@/types'
import { useRoute } from '@/composables/useRoute'
import { UserPermission } from '@/enums/UserPermission'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useAuthedUser } from '@/composables/useAuthedUser'
import { LogOut, Settings, ChartLine, Shield, BriefcaseBusiness, NotebookPen, Star, Activity } from 'lucide-vue-next'
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'

interface Props {
    user: User;
}

const { hasPermission } = useAuthedUser()

const page = usePage()

const handleLogout = () => {
    router.flushAll()
    window.location = useRoute('login')
}

defineProps<Props>()
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 text-left text-sm py-1.5">
            <UserInfo
                :user="user"
                :show-email="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuGroup>
        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full"
                :href="useRoute('user.notes.index')"
                prefetch>
                <NotebookPen class="mr-2 h-4 w-4" />
                Notes
            </Link>
        </DropdownMenuItem>

        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full"
                :href="useRoute('user.reviews.index')"
                prefetch>
                <Star class="mr-2 h-4 w-4" />
                Reviews
            </Link>
        </DropdownMenuItem>

        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full"
                :href="useRoute('user.activities.index')"
                prefetch>
                <Activity class="mr-2 h-4 w-4" />
                Activities
            </Link>
        </DropdownMenuItem>

        <DropdownMenuItem :as-child="true">
            <Link
                class="block w-full"
                :href="useRoute('user.settings.profile.edit')"
                prefetch>
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>

        <DropdownMenuItem
            v-if="hasPermission(UserPermission.VIEW_ADMIN_PANEL)"
            :as-child="true">
            <a
                class="block w-full"
                target="_blank"
                href="/admin">
                <Shield class="mr-2 h-4 w-4" />
                Admin
            </a>
        </DropdownMenuItem>

        <DropdownMenuItem
            v-if="hasPermission(UserPermission.VIEW_ANALYTICS)"
            :as-child="true">
            <a
                class="block w-full"
                target="_blank"
                :href="`https://dashboard.pirsch.io/?domain=${page.props.app.domain}&start=600&interval=live&scale=day`">
                <ChartLine class="mr-2 h-4 w-4" />
                Analytics
            </a>
        </DropdownMenuItem>

        <DropdownMenuItem
            v-if="hasPermission(UserPermission.VIEW_HORIZON_PANEL)"
            :as-child="true">
            <a
                class="block w-full"
                target="_blank"
                href="/horizon">
                <BriefcaseBusiness class="mr-2 h-4 w-4" />
                Horizon
            </a>
        </DropdownMenuItem>
    </DropdownMenuGroup>
    <DropdownMenuSeparator />
    <DropdownMenuItem :as-child="true">
        <Link
            class="block w-full"
            method="post"
            :href="useRoute('logout')"
            as="button"
            @click="handleLogout">
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
