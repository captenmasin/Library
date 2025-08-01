<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'

defineOptions({ layout: AppLayout })
</script>

<template>
    <div>
        <PageTitle>
            Privacy Policy
        </PageTitle>
        <div class="prose max-w-none mt-2">
            <p class="text-sm text-muted-foreground">
                Updated: August 1, 2025
            </p>
        </div>
    </div>
</template>
