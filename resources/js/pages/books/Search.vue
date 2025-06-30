<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DefaultCover from '~/images/default-cover.svg'
import BarcodeScanner from '@/components/books/BarcodeScanner.vue'
import { BookApiResult } from '@/types/book'
import { watchDebounced } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { Skeleton } from '@/components/ui/skeleton'
import { Input } from '@/components/ui/input/index.js'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { Button } from '@/components/ui/button/index.js'
import { computed, onMounted, PropType, ref } from 'vue'
import { Deferred, Link, router } from '@inertiajs/vue3'
import { useUserBookStatus } from '@/composables/useUserBookStatus.js'
import { Dialog, DialogClose, DialogContent, DialogFooter, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index.js'

const props = defineProps({
    results: {
        type: Object as PropType<{ total: number, books: BookApiResult[] }>,
        default: () => ({ total: 0, books: [] })
    },
    perPage: {
        type: Number,
        default: 10
    },
    page: {
        type: Number,
        default: 1
    },
    initialQuery: {
        type: String,
        default: ''
    },
    initialAuthor: {
        type: String,
        default: ''
    }
})

const query = ref(props.initialQuery)
const author = ref(props.initialAuthor)
const recent = ref([])
const loading = ref(false)

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } = useUserBookStatus()

function searchBooks () {
    router.get(useRoute('books.search'), {
        q: query.value,
        author: author.value
    }, {
        preserveState: true,
        preserveScroll: true,
        onBefore: () => {
            loading.value = true
        },
        onFinish: () => {
            loading.value = false
        }
    })
}

function loadMore () {
    router.reload({
        data: {
            page: props.page + 1
        },
        only: ['results', 'page'],
        onBefore: () => {
            loading.value = true
        },
        onFinish: () => {
            loading.value = false
        }
    })
}

const displayPage = computed(() => {
    return props.page
})

const maxPage = computed(() => {
    return Math.ceil(props.results.total / props.perPage)
})

const hasSearch = computed(() => {
    return (props.initialQuery !== '' && props.initialQuery !== null) ||
        (props.initialAuthor !== '' && props.initialAuthor !== null)
})

function select (book: BookApiResult, status: UserBookStatus) {
    if (book?.identifier) {
        if (addedBookIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        } else {
            addBookToUser(book.identifier, status)
        }
    }
}

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <h2 class="font-serif text-3xl font-bold">
                Search Books
            </h2>

            <Dialog>
                <DialogTrigger as-child>
                    <Button
                        class="cursor-pointer"
                        variant="secondary">
                        <Icon
                            name="ScanBarcode"
                            class="w-4" /> Scan barcode
                    </Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-lg">
                    <DialogTitle>Add via barcode</DialogTitle>
                    <BarcodeScanner />
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button">
                                Close
                            </Button>
                        </DialogClose>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>

        <div class="mt-8 flex items-start gap-4">
            <aside class="w-64">
                <form
                    class="flex flex-col gap-2"
                    @submit.prevent="searchBooks">
                    <Input
                        v-model="query"
                        name="query"
                        placeholder="Keywords or title..." />
                    <Input
                        id="author-search"
                        v-model="author"
                        class="w-full"
                        name="author"
                        placeholder="Author..." />
                    <Button>
                        Search
                    </Button>
                </form>
            </aside>
            <section class="flex flex-1 flex-col">
                {{ results.total }} results found
                <hr>
                {{ displayPage }} of {{ maxPage }}
                <hr>

                <div
                    v-if="!hasSearch"
                    class="text-gray-500">
                    <p>
                        Search for books by title or author, or scan a book's barcode to add it to your library.
                    </p>
                </div>

                <Deferred
                    v-else
                    :data="['results']">
                    <template #fallback>
                        <Loader
                            class="w-12 mx-auto" />
                    </template>

                    <ul
                        v-if="results && results.total > 0"
                        class="divide-y divide-gray-200">
                        <li
                            v-for="book in hasSearch ? results.books : recent"
                            :key="book.identifier">
                            <div class="flex items-center gap-4 p-2 ">
                                <div class="aspect-book w-16 shrink-0 shadow-sm rounded-sm overflow-hidden">
                                    <img
                                        :src="book.cover ?? DefaultCover"
                                        :alt="`Book cover image for ${book.title}`"
                                        class="size-full bg-gray-200 object-cover">
                                </div>
                                <div class="flex flex-col gap-1">
                                    <Link :href="book.link">
                                        <h3 class="font-serif">
                                            {{ book.title }}
                                        </h3>
                                    </Link>
                                    <p class="text-sm text-gray-500">
                                        By {{ book.authors ? book.authors.join(', ') : 'Unknown Author' }}
                                    </p>
                                </div>
                                <div class="ml-auto flex w-78 shrink-0 justify-end items-center gap-2">
                                    <div
                                        v-if="addingBooks.includes(book.identifier)"
                                        class="rounded-full border p-1 animate-spin border-gray-200 bg-gray-100 text-gray-600"
                                    >
                                        <Icon
                                            name="LoaderCircle"
                                            class="w-4"
                                        />
                                    </div>

                                    <Button
                                        v-if="addedBookIdentifiers.has(book.identifier)"
                                        @click="removeBookFromUser(book)">
                                        Remove
                                    </Button>

                                    <div class="w-44">
                                        <Select
                                            v-model="selectedStatuses[book.identifier]"
                                            @update:model-value="value => select(book, value as UserBookStatus)">
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
                        </li>
                    </ul>
                    <div v-else>
                        <p class="text-center text-gray-500">
                            No results found.
                        </p>
                    </div>
                    <div v-if="loading">
                        <Skeleton class="w-[100px] h-5 rounded-full" />
                    </div>
                    <div
                        v-if="results.total > 0 && results.books.length < results.total"
                        class="flex items-center justify-center">
                        <Button
                            variant="secondary"
                            @click="loadMore">
                            <Icon
                                v-if="!loading"
                                name="Plus"
                                class="w-4" />
                            <Icon
                                v-if="loading"
                                name="LoaderCircle"
                                class="w-4 animate-spin" />
                            Load {{ perPage }} more
                        </Button>
                    </div>
                </Deferred>
            </section>
        </div>
    </div>
</template>
