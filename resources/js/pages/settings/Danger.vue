<script setup lang="ts">
import useEmitter from '@/composables/useEmitter'
import DeleteUser from '@/components/DeleteUser.vue'
import InputError from '@/components/InputError.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import AppLayout from '@/layouts/app/AppHeaderLayout.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { getInitials } from '@/composables/useInitials'
import { type BreadcrumbItem, type User } from '@/types'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>()

const passwordInput = ref<HTMLInputElement | null>(null)

const form = useForm({
    password: ''
})

const deleteUser = (e: Event) => {
    e.preventDefault()

    form.delete(useRoute('user.settings.profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset()
    })
}

const closeModal = () => {
    form.clearErrors()
    form.reset()
}

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Delete account"
                    description="Delete your account and all of its resources" />
                <div class="space-y-4 rounded-lg border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                    <div class="relative space-y-0.5 text-red-600 dark:text-red-100">
                        <p class="font-medium">
                            Warning
                        </p>
                        <p class="text-sm">
                            Please proceed with caution, this cannot be undone.
                        </p>
                    </div>
                    <Dialog>
                        <DialogTrigger as-child>
                            <Button variant="destructive">
                                Delete account
                            </Button>
                        </DialogTrigger>
                        <DialogContent>
                            <form
                                class="space-y-6"
                                @submit="deleteUser">
                                <DialogHeader class="space-y-3">
                                    <DialogTitle>Are you sure you want to delete your account?</DialogTitle>
                                    <DialogDescription>
                                        Once your account is deleted, all of its resources and data will also be permanently deleted. Please enter your
                                        password to confirm you would like to permanently delete your account.
                                    </DialogDescription>
                                </DialogHeader>

                                <div class="grid gap-2">
                                    <Label
                                        for="password"
                                        class="sr-only">Password</Label>
                                    <Input
                                        id="password"
                                        ref="passwordInput"
                                        v-model="form.password"
                                        type="password"
                                        name="password"
                                        placeholder="Password" />
                                    <InputError :message="form.errors.password" />
                                </div>

                                <DialogFooter class="gap-2">
                                    <DialogClose as-child>
                                        <Button
                                            variant="secondary"
                                            @click="closeModal">
                                            Cancel
                                        </Button>
                                    </DialogClose>

                                    <Button
                                        type="submit"
                                        variant="destructive"
                                        :disabled="form.processing">
                                        Delete account
                                    </Button>
                                </DialogFooter>
                            </form>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </SettingsLayout>
    </div>
</template>
