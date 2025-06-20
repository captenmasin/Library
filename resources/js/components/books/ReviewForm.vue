<script setup lang="ts">
import { Book } from '@/types/book'
import { computed, PropType } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'

const props = defineProps({
    book: Object as PropType<Book>,
    existingReview: Object
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
            <textarea
                id="content"
                v-model="form.content"
                class="w-full border rounded p-2"
                rows="4"
                placeholder="Write your thoughts..."
            />
        </div>

        <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            :disabled="form.processing"
        >
            {{ hasExistingReview ? 'Update Review' : 'Submit Review' }}
        </button>
    </form>
</template>
