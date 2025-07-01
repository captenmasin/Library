<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

defineProps<Props>()
</script>

<template>
    <header>
        <h3 class="text-base font-medium mb-0.5">
            {{ title }}
        </h3>
        <p
            v-if="description"
            class="text-sm text-muted-foreground">
            {{ description }}
        </p>
    </header>
</template>
