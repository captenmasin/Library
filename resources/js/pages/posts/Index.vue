<script setup lang="ts">
import AppLayout from '@/layouts/app/AppHeaderLayout.vue'
import { PropType } from 'vue'
import { Post } from '@/types/post'

defineProps({
    posts: {
        type: Array as PropType<Post[]>,
        required: true
    }
})

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <h1>Posts</h1>
        <ul>
            <li
                v-for="post in posts"
                :key="post.uuid">
                <img
                    v-if="post.featured_image"
                    class="w-12"
                    :src="post.featured_image"
                    alt="Post Image">
                <h2>{{ post.title }}</h2>
                <p>{{ post.content }}</p>
            </li>
        </ul>
    </div>
</template>
