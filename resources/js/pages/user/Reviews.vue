<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import SingleReview from '@/components/SingleReview.vue'
import { PropType } from 'vue'
import { Review } from '@/types/review'

defineOptions({ layout: AppLayout })

const props = defineProps({
    reviews: {
        type: Array as PropType<Review[]>,
        default: () => []
    }
})
</script>

<template>
    <div>
        <PageTitle class="mb-4">Your Reviews</PageTitle>

        <ul class="divide-y divide-muted rounded-xl bg-white shadow">
            <li
                v-for="review in props.reviews"
                :key="review.uuid"
                class="p-4 flex flex-col gap-4 md:flex-row"
            >
                <BookCardHorizontal
                    :book="review.book"
                    :include-actions="false"
                    class="md:w-1/3"
                />
                <SingleReview
                    :book="review.book"
                    :review="review"
                    class="flex-1"
                />
            </li>
        </ul>
    </div>
</template>
