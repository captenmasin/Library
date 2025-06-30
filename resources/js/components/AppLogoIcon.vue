<script setup lang="ts">
import Logo from '~/images/logo.svg?raw'

defineProps({
    white: {
        type: Boolean,
        default: false
    }
})
</script>

<template>
    <div
        v-bind="$attrs"
        :style="white ? '--color-primary: #fff; --color-secondary: #fff;' : ''"
        class="flex items-center justify-center"
        v-html="Logo" />
</template>
