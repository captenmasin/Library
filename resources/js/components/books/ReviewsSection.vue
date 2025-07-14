<script setup lang="ts">
import SingleReview from '@/components/SingleReview.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import { Book } from '@/types/book'
import { Review } from '@/types/review'
import { computed, PropType, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { usePlural } from '@/composables/usePlural'
import { Separator } from '@/components/ui/separator'
import { useMarkdown } from '@/composables/useMarkdown'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    reviews: {
        type: Array as PropType<Review[]>,
        default: () => []
    }
})

const showAllReviews = ref(false)
const displayLimit = ref(3)
const displayForm = ref(false)

const displayReviews = computed(() => {
    if (!props.reviews) {
        return []
    }
    if (showAllReviews.value) {
        return props.reviews
    }

    return props.reviews.slice(0, displayLimit.value)
})
</script>

<template>
    <div class="flex flex-col">
        <ReviewForm
            :book="book"
            :existing-review="book.user_review" />

        <div class="flex flex-col divide-y divide-muted">
            <SingleReview
                v-for="review in displayReviews"
                :key="review.uuid"
                :review="review"
                :book="book" />
        </div>

        <div
            v-if="reviews && reviews?.length > displayLimit"
            class="my-3 flex items-center md:my-6"
        >
            <Separator class="flex flex-1 bg-muted" />
            <div class="flex px-4">
                <Button
                    variant="link"
                    size="sm"
                    class="text-secondary-foreground"
                    @click="showAllReviews = !showAllReviews">
                    {{ showAllReviews ? 'Show less' : 'Show all' }}
                </Button>
            </div>
            <Separator class="flex flex-1 bg-muted" />
        </div>
    </div>
</template>
