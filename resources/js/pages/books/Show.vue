<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import TagForm from '@/components/books/TagForm.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import { toast } from 'vue-sonner'
import { Review } from '@/types/review'
import type { Book } from '@/types/book'
import { type PropType, watch } from 'vue'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { Link, router, useForm } from '@inertiajs/vue3'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    book: { type: Object as PropType<Book>, required: true },
    reviews: {
        type: Array as PropType<Review[]>,
        default: () => []
    }
})

const { possibleStatuses, updateStatus } = useUserBookStatus()

const statusForm = useForm({
    status: props.book.user_status
})

function removeBook () {
    router.delete(useRoute('users.books.destroy', props.book), {
        onSuccess: () => {
            toast.success('Book removed from library')
        },
        onError: (error) => {
            toast.error(error.response.data.message || 'Failed to remove book')
        }
    })
}

watch(
    () => statusForm.status,
    (newStatus, oldStatus) => {
        if (newStatus && newStatus !== oldStatus) {
            updateStatus(props.book, newStatus)
        }
    }
)

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <div class="flex">
            <div>
                <UpdateBookCover
                    v-if="book.in_library"
                    :book
                />
                {{ book.authors }}
                {{ book.title }}

                <Select
                    v-if="book.in_library"
                    v-model="statusForm.status">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select status" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem
                                v-for="status in possibleStatuses"
                                :key="status.value"
                                :value="status.value">
                                {{ status.label }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <form
                    v-if="book.in_library"
                    @submit.prevent="removeBook">
                    <Button
                        type="submit"
                        variant="destructive">
                        Remove from library
                    </Button>
                </form>
            </div>
            <div>
                <div
                    class="prose"
                    v-html="book.description" />
                <hr>
                <NoteForm
                    v-if="book.in_library"
                    :book="book" />

                <TagForm :book="book" />

                <ReviewForm
                    :book="book"
                    :existing-review="book.user_review " />

                {{ reviews }}
            </div>
        </div>
    </div>
</template>
