<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import SingleReview from '@/components/SingleReview.vue'
import Pagination from '@/components/Pagination.vue'
import Icon from '@/components/Icon.vue'
import { PropType, ref } from 'vue'
import { Review } from '@/types/review'
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
        <PageTitle class="mb-4">Your Reviews</PageTitle>

        <ul class="divide-y divide-muted rounded-xl bg-white shadow">
            <li
                v-for="review in props.reviews.data"
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
        <Pagination :meta="props.reviews.meta" />
    </div>
</template>
