<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Image from '@/components/Image.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import { type PropType } from 'vue'
import { Link } from '@inertiajs/vue3'
import type { Book } from '@/types/book'
import { useRoute } from '@/composables/useRoute'

defineProps({
    book: { type: Object as PropType<Book>, required: true },
    reviews: {
        type: Array as PropType<any>,
        required: false,
        default: () => []
    }
})

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <UpdateBookCover :book />
        {{ book.author }}
        {{ book.title }}
        <div
            class="prose"
            v-html="book.description" />
        <hr>
        <NoteForm :book="book" />
        <ReviewForm
            :book="book"
            :existing-review="book.user_review" />

        {{ reviews }}
        <hr>
        <Link
            as="button"
            :href="useRoute('books.read.toggle', book)"
            method="post"
            class="aspect-square rounded-full bg-green-100 p-1.5 text-green-500 transition-all hover:bg-green-200"
        >
            <Icon
                :name="book.is_read ? 'x' : 'check'"
                class="w-4" />
        </Link>
    </div>
</template>
