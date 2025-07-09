<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue'
import type { User } from '@/types'
import { Link, router } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { UserPermission } from '@/enums/UserPermission'
import { useAuthedUser } from '@/composables/useAuthedUser'
import { LogOut, Settings, Shield, BriefcaseBusiness } from 'lucide-vue-next'
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'

interface Props {
    user: User;
}

const { hasPermission } = useAuthedUser()

const handleLogout = () => {
    router.flushAll()
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
                :href="useRoute('user.settings.profile.edit')"
                prefetch
                as="button">
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
