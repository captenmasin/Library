<script setup lang="ts">
import TextLink from '@/components/TextLink.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import InputError from '@/components/InputError.vue'
import { useForm } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { LoaderCircle } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'

defineProps<{
    status?: string;
}>()

const form = useForm({
    email: ''
})

const submit = () => {
    form.post(useRoute('password.email'))
}
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email to receive a password reset link">
        <div
            v-if="status"
            class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        v-model="form.email"
                        type="email"
                        name="email"
                        autocomplete="off"
                        autofocus
                        placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="my-6 flex items-center justify-start">
                    <Button
                        class="w-full"
                        :disabled="form.processing">
                        <LoaderCircle
                            v-if="form.processing"
                            class="h-4 w-4 animate-spin" />
                        Email password reset link
                    </Button>
                </div>
            </form>

            <div class="text-center text-sm space-x-1 text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="useRoute('login')">
                    log in
                </TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
