<script setup lang="ts">
import { Tag } from '@/types/tag'
import { PropType, ref, toRef } from 'vue'

const props = defineProps({
    tags: {
        type: Array as PropType<Tag[]>,
        required: true
    },
    limit: {
        type: Number,
        default: 5
    }
})

const tagsLimit = ref(props.limit)
</script>

<template>
    <ul class="space-y-1 space-x-1">
        <li
            v-for="tag in tags.slice(0, tagsLimit)"
            :key="tag.id"
            class="inline-block rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
        >
            {{ tag.name }}
        </li>
        <li
            v-if="tags.length > tagsLimit"
            class="inline-block">
            <button
                class="cursor-pointer rounded-full bg-primary/10 px-2 py-0.5 text-xs text-primary hover:bg-primary/20"
                @click="tagsLimit = 999"
            >
                +{{ tags.length - tagsLimit }} more
            </button>
        </li>
    </ul>
</template>
