<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import AppLayout from '@/layouts/app/AppHeaderLayout.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'
import { ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label'
import { Switch } from '@/components/ui/switch'
import { useUserSettings } from '@/composables/useUserSettings'

const page = usePage()
const { updateMultipleSettings } = useUserSettings()

const formError = ref(null)

const settings = ref({
    'library.tilt_books': page.props.auth.user.settings?.library?.tilt_books
})

function submitForm () {
    updateMultipleSettings(settings.value)
        .catch(error => {
            formError.value = error?.message || 'An error occurred while updating settings.'
        })
}

watch(
    () => settings,
    (newVal, oldVal) => {
        submitForm()
    },
    { deep: true }
)

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <SettingsLayout>
            <div class="flex flex-col gap-4">
                <label
                    for="library-tilt"
                    class="flex flex-row items-center justify-between rounded-lg border p-4">
                    <div class="space-y-0.5">
                        <Label class="text-base">
                            Tilt
                        </Label>
                        <p class="text-sm text-muted-foreground">
                            Tiiiilt
                        </p>
                    </div>
                    <div>
                        <Switch
                            id="library-tilt"
                            v-model="settings['library.tilt_books']" />
                    </div>
                </label>

                <InputError
                    v-if="formError"
                    :message="formError"
                    class="mt-2" />
            </div>
        </SettingsLayout>
    </div>
</template>
