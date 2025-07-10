<script setup lang="ts">
import Image from '@/components/Image.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import BookCard from '@/components/books/BookCard.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import RatingForm from '@/components/books/RatingForm.vue'
import BookActions from '@/components/books/BookActions.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import { Review } from '@/types/review'
import type { Book } from '@/types/book'
import { type PropType, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePlural } from '@/composables/usePlural'
import { useMarkdown } from '@/composables/useMarkdown'

const props = defineProps({
    book: { type: Object as PropType<Book>, required: true },
    related: { type: Array as PropType<Book[]> },
    reviews: {
        type: Array as PropType<Review[]>,
        default: () => []
    }
})

const data = [
    {
        title: 'Pages',
        value: props.book.page_count || 'N/A'
    },
    {
        title: 'Publisher',
        value: props.book.publisher?.name || 'N/A'
    },
    {
        title: 'Published',
        value: props.book.published_date || 'N/A'
    }
]

const tagsLimit = ref(5)

const refreshKey = ref(1)

function refresh () {
    router.reload({
        onSuccess: () => {
            refreshKey.value += 1
        }
    })
}

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <div class="flex gap-10">
            <div class="flex w-1/5 flex-col">
                <UpdateBookCover :book>
                    <div class="aspect-book rounded-md overflow-hidden">
                        <Image
                            width="250"
                            class="size-full object-cover"
                            :src="book.cover" />
                    </div>
                </UpdateBookCover>
                <div
                    v-if="book.in_library"
                    class="mt-4 flex flex-col items-center">
                    <h3 class="text-lg font-semibold hidden">
                        Your rating
                    </h3>
                    <RatingForm
                        :key="refreshKey"
                        class="mt-1"
                        :book="book"
                        @deleted="refresh"
                        @added="refresh"
                        @updated="refresh" />
                </div>
            </div>
            <div class="flex w-3/5 flex-col">
                <h2 class="font-serif text-3xl font-semibold">
                    {{ book.title }}
                </h2>
                <p
                    v-if="book.authors"
                    class="mt-1 text-sm text-muted-foreground">
                    By {{ book.authors.map((a) => a.name).join(', ') }}
                </p>
                <div
                    class="mt-4 max-w-none font-serif prose"
                    v-html="useMarkdown(book.description)" />

                <div class="mt-8">
                    <div v-if="book.in_library">
                        <h3 class="mt-6 text-lg font-semibold">
                            Your notes
                        </h3>
                        <NoteForm
                            class="mt-1"
                            :book="book" />
                    </div>
                    <ReviewForm
                        :book="book"
                        :existing-review="book.user_review" />
                </div>
                <hr class="mt-12">
                <div class="mt-12">
                    Reviwssssss:

                    <div
                        v-if="book.average_rating && book.ratings_count"
                        class="text-sm/6 mt-2 flex items-center text-center">
                        Average: {{ book.average_rating }}<br> stars
                        {{ book.ratings_count }} {{ usePlural('rating', book.ratings_count) }}
                    </div>

                    <ul>
                        <li
                            v-for="review in reviews"
                            :key="review.uuid"
                            class="mt-4">
                            <div
                                class="prose"
                                v-html="useMarkdown(review.content)" />
                        </li>
                    </ul>
                </div>
            </div>
            <div class="flex w-1/5 flex-col">
                <div>
                    <BookActions
                        :book="book"
                        @removed="refresh"
                        @added="refresh"
                        @updated="refresh" />
                </div>
                <h3 class="text-lg mt-4 font-semibold">
                    Details
                </h3>
                <dl>
                    <div
                        v-for="item in data"
                        :key="item.title"
                        class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="font-medium text-sm/6">
                            {{ item.title }}
                        </dt>
                        <dd class="text-right text-sm/6 text-muted-foreground sm:col-span-2 sm:mt-0">
                            {{ item.value }}
                        </dd>
                    </div>
                </dl>

                <div
                    v-if="book.tags && book.tags.length > 0"
                    class="mt-1">
                    <p class="font-medium text-sm/6">
                        Tags
                    </p>
                    <ul class="space-y-1 space-x-1">
                        <li
                            v-for="tag in book.tags.slice(0, tagsLimit)"
                            :key="tag"
                            class="inline-block rounded-full px-2 text-xs bg-muted py-0.5 text-muted-foreground"
                        >
                            {{ tag }}
                        </li>
                        <li
                            v-if="book.tags.length > tagsLimit"
                            class="inline-block">
                            <button
                                class="rounded-full px-2 text-xs bg-primary/10 hover:bg-primary/20 text-primary cursor-pointer py-0.5"
                                @click="tagsLimit = 999">
                                +{{ book.tags.length - tagsLimit }} more
                            </button>
                        </li>
                    </ul>
                </div>

                <div
                    v-if="related && related.length > 0"
                    class="mt-4">
                    <p class="font-medium text-sm/6">
                        Related
                    </p>
                    <div class="flex -mx-2 flex-wrap">
                        <div
                            v-for="relatedBook in related"
                            :key="relatedBook.identifier"
                            class="w-1/2 p-2">
                            <BookCard
                                :hover="false"
                                :book="relatedBook" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
