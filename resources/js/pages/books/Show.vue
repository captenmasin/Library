<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import { toast } from 'vue-sonner'
import type { Book } from '@/types/book'
import { useForm } from '@inertiajs/vue3'
import { type PropType, watch } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { UserBookStatusEnum } from '@/enums/UserBookStatusEnum'
import { UserBookStatusOption } from '@/types/user-book-status'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    book: { type: Object as PropType<Book>, required: true },
    reviews: {
        type: Array as PropType<any>,
        required: false,
        default: () => []
    },
    initialUserBookStatus: {
        type: String as PropType<UserBookStatusEnum>,
        required: false,
        default: null
    },
    userBookStatuses: {
        type: Array as PropType<Array<UserBookStatusOption>>,
        required: false,
        default: () => []
    }
})

const statusForm = useForm({
    status: props.initialUserBookStatus
})

watch(
    () => statusForm.status,
    (newStatus, oldStatus) => {
        if (newStatus && newStatus !== oldStatus) {
            statusForm.patch(useRoute('books.update', props.book), {
                preserveScroll: true
            })
        }
    }
)

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <UpdateBookCover :book />
        {{ book.author }}
        {{ book.title }}

        <Select v-model="statusForm.status">
            <SelectTrigger class="w-full">
                <SelectValue placeholder="Select status" />
            </SelectTrigger>
            <SelectContent>
                <SelectGroup>
                    <SelectItem
                        v-for="status in userBookStatuses"
                        :key="status.value"
                        :value="status.value">
                        {{ status.label }}
                    </SelectItem>
                </SelectGroup>
            </SelectContent>
        </Select>
        <div
            class="prose"
            v-html="book.description" />
        <hr>
        <NoteForm :book="book" />
        <ReviewForm
            :book="book"
            :existing-review="book.user_review" />

        {{ reviews }}
    </div>
</template>
