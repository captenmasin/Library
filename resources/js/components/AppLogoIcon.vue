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
    <!--    <img-->
    <!--        :src="TestLogo"-->
    <!--        alt="Logo"-->
    <!--        v-bind="$attrs"-->
    <!--    >-->
    <div
        v-bind="$attrs"
        class="flex items-center justify-center overflow-hidden"
        v-html="Logo" />
</template>
