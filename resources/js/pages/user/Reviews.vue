<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import SingleReview from '@/components/SingleReview.vue'
import Icon from '@/components/Icon.vue'
import { PropType, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button/index.js'
import { Review } from '@/types/review'
import { Paginated } from '@/types/pagination'

defineOptions({ layout: AppLayout })

const props = defineProps({
    reviews: {
        type: Object as PropType<Paginated<Review>>,
        default: () => ({ data: [], links: {}, meta: {} })
    }
})

const loadingMore = ref(false)

function loadMore () {
    router.reload({
        data: { page: props.reviews.meta.current_page + 1 },
        only: ['reviews'],
        preserveState: true,
        preserveScroll: true,
        onBefore: () => { loadingMore.value = true },
        onFinish: () => { loadingMore.value = false }
    })
}
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
        <div
            v-if="props.reviews.links.next"
            class="mt-4 mb-36 flex items-center justify-center"
        >
            <Button
                variant="secondary"
                :disabled="loadingMore"
                @click="loadMore"
            >
                <Icon
                    v-if="!loadingMore"
                    name="Plus"
                    class="w-4" />
                <Icon
                    v-else
                    name="LoaderCircle"
                    class="w-4 animate-spin" />
                Load more
            </Button>
        </div>
    </div>
</template>
