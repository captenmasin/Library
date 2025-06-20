<script lang="ts" setup>
import { Toaster as Sonner, type ToasterProps } from 'vue-sonner'

const props = defineProps<ToasterProps>()
</script>

<template>
    <Sonner
        class="toaster group"
        v-bind="props"
        position="bottom-center"
        :toast-options="{
            classes: {
                toast: 'app-toast',
                description: 'group-[.toast]:text-muted-foreground',
                actionButton:
                    'group-[.toast]:bg-primary group-[.toast]:text-primary-foreground',
                cancelButton:
                    'group-[.toast]:bg-muted group-[.toast]:text-muted-foreground',
            },
        }"
    />
</template>
