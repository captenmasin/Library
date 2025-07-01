<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Image from '@/components/Image.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import TagForm from '@/components/books/TagForm.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import ReviewForm from '@/components/books/ReviewForm.vue'
import UpdateBookCover from '@/components/books/UpdateBookCover.vue'
import PlaceholderPattern from '@/components/PlaceholderPattern.vue'
import { Review } from '@/types/review'
import type { Book } from '@/types/book'
import { Button } from '@/components/ui/button'
import { type PropType, ref, watch } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { useMarkdown } from '@/composables/useMarkdown'
import { Link, router, useForm } from '@inertiajs/vue3'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    book: { type: Object as PropType<Book>, required: true },
    reviews: {
        type: Array as PropType<Review[]>,
        default: () => []
    }
})

const { possibleStatuses, updateStatus, addBookToUser, removeBookFromUser } = useUserBookStatus()

const statusForm = useForm({
    status: props.book.user_status
})

function reset () {
    statusForm.status = null
    router.reload()
}

watch(
    () => statusForm.status,
    (newStatus, oldStatus) => {
        if (newStatus && newStatus !== oldStatus) {
            if (props.book?.in_library) {
                updateStatus(props.book, newStatus, () => router.reload())
            } else {
                addBookToUser(props.book.identifier, newStatus, () => router.reload())
            }
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
            <div class="flex w-1/5 flex-col">
                <UpdateBookCover :book>
                    <Image
                        width="250"
                        class="w-full rounded-md aspect-cover"
                        :src="book.cover" />
                </UpdateBookCover>

                <div class="mt-4">
                    <div class="flex flex-col">
                        <Select v-model="statusForm.status">
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
                        <div class="flex justify-end">
                            <Button
                                v-if="book.in_library"
                                class="flex text-xs text-destructive"
                                variant="link"
                                @click="removeBookFromUser(book, reset)">
                                <Icon
                                    name="trash"
                                    class="w-3" />
                                Remove
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex w-3/5 flex-col">
                <h2 class="mt-2 font-serif text-3xl font-semibold">
                    {{ book.title }}
                </h2>
                <p
                    v-if="book.authors"
                    class="mt-1 text-sm text-muted-foreground">
                    By {{ book.authors.map((a) => a.name).join(', ') }}
                </p>
                <div
                    class="mt-4 max-w-none font-serif prose"
                    v-html="book.description" />
                <div class="mt-8">
                    <!--                    <TagForm :book="book" />-->

                    <ReviewForm
                        :book="book"
                        :existing-review="book.user_review" />
                </div>
                <hr class="mt-12">
                <div class="mt-12">
                    Reviwssssss:
                    <ul>
                        <li
                            v-for="review in reviews"
                            :key="review.uuid"
                            class="mt-4">
                            <div
                                class="prose"
                                v-html="useMarkdown(review.content)" />
                        </li>
                    </ul>
                </div>
            </div>
            <div class="flex w-1/5 flex-col">
                <h3 class="text-lg font-semibold">
                    Details
                </h3>
                <dl>
                    <div
                        v-for="item in data"
                        :key="item.title"
                        class="px-4 py-2 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="font-medium text-sm/6">
                            {{ item.title }}
                        </dt>
                        <dd class="text-right text-sm/6 text-muted-foreground sm:col-span-2 sm:mt-0">
                            {{ item.value }}
                        </dd>
                    </div>
                </dl>
                <div
                    v-if="book.categories"
                    class="mt-1">
                    <p class="font-medium text-sm/6">
                        Categories
                    </p>
                    <ul class="space-y-1 space-x-1">
                        <li
                            v-for="category in book.categories"
                            :key="category"
                            class="inline-block rounded-full px-2 text-xs bg-muted py-0.5 text-muted-foreground"
                        >
                            {{ category }}
                        </li>
                    </ul>
                </div>
                <div v-if="book.in_library">
                    <h3 class="mt-8 text-lg font-semibold">
                        Your notes
                    </h3>
                    <NoteForm
                        :book="book" />
                </div>
            </div>
        </div>
    </div>
</template>
