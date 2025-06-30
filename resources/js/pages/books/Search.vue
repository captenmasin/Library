<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import DefaultCover from '~/images/default-cover.svg'
import AppLayout from '@/layouts/app/AppHeaderLayout.vue'
import BarcodeScanner from '@/components/books/BarcodeScanner.vue'
import { BookApiResult } from '@/types/book'
import { watchDebounced } from '@vueuse/core'
import { computed, PropType, ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { Deferred, router } from '@inertiajs/vue3'
import { Skeleton } from '@/components/ui/skeleton'
import { Input } from '@/components/ui/input/index.js'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { Button } from '@/components/ui/button/index.js'
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
    query: {
        type: String,
        default: ''
    },
    author: {
        type: String,
        default: ''
    }
})

const keyword = ref(props.query)
const author = ref(props.author)
const recent = ref([])
const loading = ref(false)

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } = useUserBookStatus()

function searchBooks () {
    router.get(useRoute('library.search'), {
        q: keyword.value,
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

watchDebounced([keyword, author], searchBooks, { debounce: 500 })

const displayPage = computed(() => {
    return props.page
})

const maxPage = computed(() => {
    return Math.ceil(props.results.total / props.perPage)
})

const hasSearch = computed(() => {
    return keyword.value !== '' || author.value !== ''
})

function select (book: BookApiResult, status: UserBookStatus) {
    if (book?.identifier) {
        if (addedBookIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        }
        addBookToUser(book.identifier, status)
    }
}

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <h2 class="font-serif text-3xl font-bold">
            Search Books
        </h2>

        <Dialog>
            <DialogTrigger as-child>
                <Button
                    class="cursor-pointer"
                    variant="secondary">
                    <Icon
                        name="Plus"
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

        <div class="mt-8 flex items-start gap-4">
            <div class="flex flex-col w-64">
                <Input
                    id="keyword-search"
                    v-model="keyword"
                    class="w-full"
                    placeholder="Search for books by title, author, or publisher" />
                <Input
                    id="author-search"
                    v-model="author"
                    class="w-full"
                    placeholder="Author..." />
            </div>
            <section class="flex flex-1 flex-col">
                <Loader
                    class="w-12 mx-auto" />

                {{ results.total }} results found
                <hr>
                {{ displayPage }} of {{ maxPage }}

                <Deferred data="results">
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
                                    <h3 class="font-serif">
                                        {{ book.title }}
                                    </h3>
                                    <!--                                    <Link-->
                                    <!--                                        :href="book.link"-->
                                    <!--                                        class="text-sm text-gray-500">-->
                                    <!--                                        {{ book.link }}-->
                                    <!--                                    </Link>-->
                                    <p class="text-sm text-gray-500">
                                        By {{ book.authors ? book.authors.join(', ') : 'Unknown Author' }}
                                    </p>
                                </div>
                                <div class="ml-auto flex items-center gap-2 px-2">
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
                        </li>
                    </ul>
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
