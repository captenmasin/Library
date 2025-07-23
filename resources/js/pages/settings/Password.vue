<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import InputError from '@/components/InputError.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'
import { toast } from 'vue-sonner'
import { PropType, ref } from 'vue'
import { UserPasskey } from '@/types/user'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { router, useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { useRequest } from '@/composables/useRequest'
import { startRegistration } from '@simplewebauthn/browser'

defineProps({
    passkeys: {
        type: Array as PropType<UserPasskey[]>
    }
})

const passwordInput = ref<HTMLInputElement | null>(null)
const currentPasswordInput = ref<HTMLInputElement | null>(null)

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: ''
})

const updatePassword = () => {
    form.put(useRoute('user.settings.password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation')
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus()
                }
            }

            if (errors.current_password) {
                form.reset('current_password')
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus()
                }
            }
        }
    })
}

async function addPassKey () {
    try {
        const response = await useRequest(useRoute('profile.passkeys.generate-options'), 'GET')

        const options = typeof response === 'string' ? JSON.parse(response) : response

        const credential = await startRegistration(options)

        router.post(useRoute('profile.passkeys.store'), {
            options: JSON.stringify(options),
            passkey: JSON.stringify(credential)
        })
    } catch (error) {
        console.log(error)
    }
}

function deletePasskey (id) {
    if (confirm('Are you SURE you want to delete this passkey?')) {
        router.delete(useRoute('profile.passkeys.delete', id))
    }
}

defineOptions({
    layout: SettingsLayout
})
</script>

<template>
    <div class="flex flex-col space-y-8">
        <form
            class="space-y-4 md:space-y-5"
            @submit.prevent="updatePassword">
            <div class="grid gap-4 grid-cols-2">
                <div class="grid gap-1">
                    <Label for="password">New password</Label>
                    <Input
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-1">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        autocomplete="new-password"
                    />
                    <InputError :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="grid gap-1">
                <Label for="current_password">Current password</Label>
                <Input
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="current-password"
                />
                <InputError :message="form.errors.current_password" />
            </div>

            <div class="flex items-center justify-end gap-4">
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
                <Button :disabled="form.processing">
                    Save password
                </Button>
            </div>
        </form>

        <HeadingSmall
            title="Passkeys"
            description="Use passkeys to log in without a password"
        />

        <div
            v-if="passkeys && passkeys?.length > 0"
            class="flex flex-col gap-6">
            <div
                v-for="passkey in passkeys"
                :key="passkey.id"
                class="flex items-center justify-between">
                <div class="flex flex-col gap-1">
                    <div class="flex">
                        <p class="font-mono bg-muted flex text-xs text-muted-foreground px-2 py-0.5 rounded-full">
                            {{ passkey.name }}
                        </p>
                    </div>
                    <p class="text-sm px-1">
                        Last used at: {{ passkey.last_used_at || 'Never' }}
                    </p>
                </div>
                <Button
                    variant="link"
                    class="text-destructive"
                    @click="deletePasskey(passkey.id)">
                    Delete
                </Button>
            </div>
        </div>

        <div class="flex justify-end">
            <Button
                variant="secondary"
                @click.prevent="addPassKey">
                Add a passkey
            </Button>
        </div>
    </div>
</template>
