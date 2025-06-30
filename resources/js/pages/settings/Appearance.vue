<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import HeadingSmall from '@/components/HeadingSmall.vue'
import SettingsLayout from '@/layouts/settings/Layout.vue'
import AppearanceTabs from '@/components/AppearanceTabs.vue'

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    title="Appearance settings"
                    description="Update your account's appearance settings" />
                <AppearanceTabs />
            </div>
        </SettingsLayout>
    </div>
</template>
