<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue'
import type { User } from '@/types'
import { Link, router } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { LogOut, Settings } from 'lucide-vue-next'
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'

interface Props {
    user: User;
}

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
