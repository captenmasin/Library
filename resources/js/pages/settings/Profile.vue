<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import useEmitter from '@/composables/useEmitter'
import InputError from '@/components/InputError.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
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

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile'
    }
]

const page = usePage()
const user = page.props.auth.user as User

const form = useForm({
    name: user.name,
    username: user.username,
    email: user.email,
    avatar: user.avatar
})

const fileInput = ref<HTMLInputElement | null>(null)
const fileInputKey = ref(0)
const previewUrl = ref<string | null>(user.avatar)

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files[0]) {
        form.avatar = target.files[0]

        form.clearErrors('avatar')
        previewUrl.value = URL.createObjectURL(target.files[0])
    }
}

const resetForm = () => {
    form.reset()
    form.clearErrors()
    fileInputKey.value++
    previewUrl.value = user.avatar
    if (fileInput.value) {
        fileInput.value.value = ''
    }
}

const submit = () => {
    form.transform((data) => {
        if (typeof data.avatar === 'string') {
            delete data.avatar
        }

        return data
    }).post(useRoute('user.settings.profile.update'), {
        preserveScroll: true,
        onSuccess: params => {
            useEmitter.emit('avatar:updated')
            toast.success('Profile updated successfully')
        }
    })
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall
                    title="Profile information"
                    description="Update your name and email address" />

                <form
                    class="space-y-6"
                    @submit.prevent="submit">
                    <div class="grid">
                        <Label for="name">Name</Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            class="mt-1 block w-full"
                            required
                            autocomplete="name"
                            placeholder="Full name" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.name" />
                    </div>

                    <div class="grid">
                        <Label for="name">Username</Label>
                        <Input
                            id="username"
                            v-model="form.username"
                            required
                            class="mt-1 block w-full"
                            autocomplete="username"
                            placeholder="Username" />
                        <InputError
                            class="mt-2"
                            :message="form.errors.username" />
                    </div>

                    <div>
                        <Label for="avatar">Avatar</Label>

                        <div class="flex w-full items-center gap-2">
                            <label for="avatar">
                                <Avatar
                                    :key="fileInputKey"
                                    class="flex overflow-hidden rounded-md border size-10 border-input-border">
                                    <AvatarImage
                                        v-if="previewUrl"
                                        :src="previewUrl"
                                        :alt="user.name" />
                                    <AvatarFallback>
                                        {{ getInitials(user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                            </label>
                            <div class="grid w-full">
                                <Input
                                    id="avatar"
                                    ref="fileInput"
                                    :key="fileInputKey"
                                    type="file"
                                    accept="image/*"
                                    @input="handleFileChange" />

                                <progress
                                    :class="form.progress ? 'opacity-100' : 'opacity-0'"
                                    :value="form.progress?.percentage"
                                    class="-mt-1 w-full px-1 form-progress-bar h-[2px]"
                                    max="100"
                                >
                                    {{ form.progress?.percentage }}%
                                </progress>
                            </div>
                        </div>
                        <InputError
                            class="ml-12"
                            :message="form.errors.avatar" />
                    </div>

                    <div class="grid">
                        <Label for="email">Email address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                            autocomplete="username"
                            placeholder="Email address"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.email" />
                    </div>

                    <div v-if="mustVerifyEmail && !user.email_verified">
                        <p class="-mt-4 text-sm text-muted-foreground">
                            Your email address is unverified.
                            <Link
                                :href="useRoute('verification.send')"
                                method="post"
                                as="button"
                                preserve-scroll
                                class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                            >
                                Click here to resend the verification email.
                            </Link>
                        </p>

                        <div
                            v-if="status === 'verification-link-sent'"
                            class="mt-2 text-sm font-medium text-green-600">
                            A new verification link has been sent to your email address.
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div
                            class="flex items-center justify-end mt-8 gap-4">
                            <Button
                                type="button"
                                variant="ghost"
                                :disabled="form.processing || !form.isDirty"
                                @click="resetForm">
                                Reset
                            </Button>

                            <Button
                                type="submit"
                                :disabled="form.processing || !form.isDirty">
                                Save
                            </Button>
                        </div>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p
                                v-show="form.recentlySuccessful"
                                class="text-sm text-neutral-600">
                                Saved.
                            </p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
