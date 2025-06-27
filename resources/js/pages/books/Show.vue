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
import { useRoute } from '@/composables/useRoute'
import { Link, router, useForm } from '@inertiajs/vue3'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { onMounted, type PropType, ref, watch } from 'vue'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    book: { type: Object as PropType<Book>, required: true },
    reviews: {
        type: Array as PropType<Review[]>,
        default: () => []
    }
})

const { possibleStatuses, updateStatus, addBookToUser } = useUserBookStatus()

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

const data = [
    {
        title: 'Pages',
        value: props.book.page_count || 'N/A'
    },
    {
        title: 'Publisher',
        value: props.book.publisher?.name || 'N/A'
    },
    {
        title: 'Published',
        value: props.book.published_date || 'N/A'
    }
]

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <div class="flex gap-8">
            <div class="flex flex-col w-1/5">
                <UpdateBookCover :book>
                    <Image
                        width="250"
                        class="rounded-md w-full aspect-cover"
                        :src="book.cover" />
                </UpdateBookCover>

                <h2 class="font-serif text-3xl mt-2 font-semibold">
                    {{ book.title }}
                </h2>
                <p
                    v-if="book.authors"
                    class="text-sm text-muted-foreground mt-1">
                    By {{ book.authors.map(a => a.name).join(', ') }}
                </p>
                <div class="mt-4">
                    <dl>
                        <div
                            v-for="item in data"
                            :key="item.title"
                            class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                            <dt class="text-sm/6 font-medium">
                                {{ item.title }}
                            </dt>
                            <dd class="text-right text-sm/6 text-muted-foreground sm:col-span-2 sm:mt-0">
                                {{ item.value }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="mt-2 w-full flex flex-col">
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
                            :on-finish="visit => statusForm.status = null"
                            :href="useRoute('user.books.destroy', props.book)">
                            Remove from library
                        </Link>
                    </Button>

                    <div v-else>
                        <Select
                            v-model="statusForm.status"
                            @update:model-value="value => addBookToUser(book, value as UserBookStatus)">
                            <SelectTrigger class="w-full">
                                <SelectValue placeholder="Add to library" />
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
                </div>
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
