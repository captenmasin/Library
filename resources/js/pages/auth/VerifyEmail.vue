<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { LoaderCircle } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Head, useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'

defineProps<{
    status?: string;
}>()

const form = useForm({})

const submit = () => {
    form.post(useRoute('verification.send'))
}
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Please verify your email address by clicking on the link we just emailed to you.">
        <div
            v-if="status === 'verification-link-sent'"
            class="mb-4 text-center text-sm font-medium text-green-600">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form
            class="text-center space-y-6"
            @submit.prevent="submit">
            <Button
                :disabled="form.processing"
                variant="secondary">
                <LoaderCircle
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin" />
                Resend verification email
            </Button>

            <TextLink
                :href="useRoute('logout')"
                method="post"
                as="button"
                class="mx-auto block text-sm">
                Log out
            </TextLink>
        </form>
    </AuthLayout>
</template>
