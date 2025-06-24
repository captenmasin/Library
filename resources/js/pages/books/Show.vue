<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import { toast } from 'vue-sonner'
import { Review } from '@/types/review'
import type { Book } from '@/types/book'
import { useForm } from '@inertiajs/vue3'
import { type PropType, watch } from 'vue'
import { useRoute } from '@/composables/useRoute'
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
            </div>
            <div>
                <div
                    class="prose"
                    v-html="book.description" />
                <hr>
                <NoteForm
                    v-if="book.in_library"
                    :book="book" />
                <ReviewForm
                    :book="book"
                    :existing-review="book.user_review " />

                {{ reviews }}
            </div>
        </div>
    </div>
</template>
