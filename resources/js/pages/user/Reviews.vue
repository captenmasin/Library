<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import SingleReview from '@/components/SingleReview.vue'
import CustomPagination from '@/components/CustomPagination.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import { PropType } from 'vue'
import { Review } from '@/types/review'
import { Card } from '@/components/ui/card'
import { Paginated } from '@/types/pagination'

defineOptions({ layout: AppLayout })

const props = defineProps({
    reviews: {
        type: Object as PropType<Paginated<Review>>,
        default: () => ({ data: [], links: {}, meta: {} })
    }
})
</script>

<template>
    <div>
        <PageTitle class="mb-4">
            Your Reviews
        </PageTitle>

        <div
            v-if="reviews.meta.total === 0 || reviews.data.length === 0"
            class="my-4 flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-primary/10 px-4 py-8 md:py-12 text-center text-sm text-muted-foreground"
        >
            <Icon
                name="NotebookPen"
                class="size-8" />
            <h3 class="font-serif text-2xl font-semibold">
                Nothing to see here
            </h3>
            <p v-if="reviews.data.length === 0">
                There's no reviews on this page
            </p>
            <p v-else>
                You haven't reviewed any books yet
            </p>
        </div>

        <ul
            v-else
            class="divide-y divide-muted rounded-xl dark:divide-zinc-950 bg-white dark:bg-zinc-900 shadow">
            <li
                v-for="review in props.reviews.data"
                :key="review.uuid"
                class="p-4 md:p-6 flex items-start group flex-col gap-4 md:flex-row"
            >
                <BookCardHorizontal
                    :book="review.book"
                    :include-actions="false"
                    class="md:w-1/3"
                />
                <SingleReview
                    :book="review.book"
                    :review="review"
                    class="flex-1 py-0"
                />
            </li>
        </ul>
        <CustomPagination
            v-if="reviews.meta.total > reviews.meta.per_page"
            class="my-4"
            :meta="props.reviews.meta" />
    </div>
</template>
