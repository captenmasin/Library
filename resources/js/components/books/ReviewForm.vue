<script setup lang="ts">
import { Book } from '@/types/book'
import { Review } from '@/types/review'
import { computed, PropType } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps({
    book: Object as PropType<Book>,
    existingReview: Object as PropType<Review | null>
})

const hasExistingReview = computed(() => !!props.existingReview)

const form = useForm({
    rating: props.existingReview?.rating || 0,
    content: props.existingReview?.content || ''
})

function submit () {
    form.post(useRoute('books.reviews.store', props.book), {
        preserveScroll: true
    })
}
</script>

<template>
    <form @submit.prevent="submit">
        <div class="mb-4">
            <label class="block mb-1">Rating</label>
            <div class="flex space-x-1">
                <button
                    v-for="star in 5"
                    :key="star"
                    type="button"
                    :aria-label="`Rate ${star} star`"
                    @click="form.rating = star"
                >
                    <span :class="star <= form.rating ? 'text-yellow-400' : 'text-gray-300'">
                        ★
                    </span>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label
                class="block mb-1"
                for="content">Review (optional)</label>
            <Textarea
                id="content"
                v-model="form.content"
                class="w-full border rounded p-2"
                rows="4"
                placeholder="Write your thoughts..."
            />
        </div>

        <Button
            type="submit"
            :disabled="form.processing"
        >
            {{ hasExistingReview ? 'Update Review' : 'Submit Review' }}
        </Button>
    </form>
</template>
