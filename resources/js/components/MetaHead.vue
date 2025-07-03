<script setup>
import { onMounted, ref } from 'vue'
import { useTitle } from '@vueuse/core'
import { Head, router, usePage } from '@inertiajs/vue3'

const page = usePage()
const meta = ref(page.props.meta)
const key = ref(new Date().toDateString())
const currentTitle = ref('')

function applyMeta (event = null) {
    meta.value = page.props.meta
    key.value = event ? (event.timeStamp + event.detail.page.component) : new Date().toDateString()
    currentTitle.value = meta.value?.title

    useTitle(meta.value?.title)

    document.querySelector('meta[name="description"]').setAttribute('content', meta.value?.description ?? '')
}

router.on('success', (event) => applyMeta(event))

onMounted(() => applyMeta())
</script>

<template>
    <Head
        :key="key"
        :description="meta?.description"
        :title="meta?.title" />
</template>
