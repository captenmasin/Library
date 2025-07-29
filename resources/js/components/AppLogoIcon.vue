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
        :style="white ? '--color-primary: #fff; --color-secondary: #fff;' : ''"
        class="flex items-center justify-center overflow-hidden"
        v-html="Logo" />
</template>
