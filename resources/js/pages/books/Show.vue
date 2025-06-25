<script setup lang="ts">
import Image from '@/components/Image.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import TagForm from '@/components/books/TagForm.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import { Review } from '@/types/review'
import type { Book } from '@/types/book'
import { Button } from '@/components/ui/button'
import { type PropType, ref, watch } from 'vue'
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

const reload = () => {
    router.reload({
        only: ['book'],
        onSuccess: () => {
            statusForm.status = props.book?.user_status
        }
    })
}

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
        <div class="flex gap-8">
            <div class="flex flex-col gap-4 w-1/5">
                <UpdateBookCover
                    v-if="book.in_library"
                    :book
                />
                <Image
                    v-else
                    width="250"
                    class="rounded-md w-full aspect-cover"
                    :src="book.cover" />

                {{ book.authors }}
                {{ book.title }}

                {{ statusForm.status }}

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

                <Button
                    v-if="book.in_library"
                    as-child
                    variant="destructive">
                    <Link
                        method="delete"
                        preserve-scroll
                        :href="useRoute('user.books.destroy', props.book)">
                        Remove from library
                    </Link>
                </Button>

                <Button
                    v-else
                    as-child
                    variant="default">
                    <Link
                        method="post"
                        :on-success="reload"
                        :data="{
                            identifier: book.identifier,
                            status: 'PlanToRead'
                        }"
                        preserve-scroll
                        :href="useRoute('user.books.store')">
                        Add to library
                    </Link>
                </Button>
            </div>
            <div class="flex flex-col w-4/5">
                <div
                    class="prose max-w-none font-serif"
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
