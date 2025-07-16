<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BarcodeScanner from '@/components/books/BarcodeScanner.vue'
import HorizontalSkeleton from '@/components/books/HorizontalSkeleton.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import { BookApiResult } from '@/types/book'
import { Label } from '@/components/ui/label'
import { useRoute } from '@/composables/useRoute'
import { Skeleton } from '@/components/ui/skeleton'
import { Separator } from '@/components/ui/separator'
import { Input } from '@/components/ui/input/index.js'
import { computed, nextTick, PropType, ref } from 'vue'
import { Button } from '@/components/ui/button/index.js'
import { Deferred, Link, router } from '@inertiajs/vue3'
import { Dialog, DialogContent, DialogTitle, DialogTrigger } from '@/components/ui/dialog'

const props = defineProps({
    results: {
        type: Object as PropType<{ total: number, books: BookApiResult[] }>,
        default: () => ({ total: 0, books: [] })
    },
    scan: {
        type: Boolean,
        default: false
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
const loadingMore = ref(false)

const showAuthorField = ref(author.value !== '' && author.value !== null)

function searchBooks () {
    router.get(useRoute('books.search'), {
        q: query.value,
        author: author.value
    }, {
        preserveState: true,
        preserveScroll: true,
        onBefore: () => {
            loadingMore.value = true
        },
        onFinish: () => {
            loadingMore.value = false
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
            loadingMore.value = true
        },
        onFinish: () => {
            loadingMore.value = false
            // window.scrollTo({
            //     top: document.documentElement.scrollHeight - 100,
            //     behavior: 'smooth'
            // })
        }
    })
}

const hasSearch = computed(() => {
    return (props.initialQuery !== '' && props.initialQuery !== null) ||
        (props.initialAuthor !== '' && props.initialAuthor !== null)
})

function formatNumber (num: number) {
    return new Intl.NumberFormat('en-US', { style: 'decimal' }).format(num)
}

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <div>
        <div class="flex items-center justify-between">
            <PageTitle>
                Search Books
            </PageTitle>
        </div>

        <div class="mt-4 flex flex-col items-start gap-8 md:mt-8 md:flex-row">
            <aside class="top-4 left-0 w-full md:sticky md:w-64">
                <form
                    class="flex flex-col gap-2"
                    @submit.prevent="searchBooks">
                    <div class="flex flex-col gap-4">
                        <div class="grid gap-2">
                            <Label for="query">Title or Keywords</Label>
                            <Input
                                id="query"
                                v-model.trim="query"
                                name="query"
                                placeholder="e.g. Dune, horror, science fiction" />
                        </div>

                        <div
                            v-if="!showAuthorField"
                            class="-mt-2 mb-4 flex">
                            <button
                                type="button"
                                class="flex cursor-pointer items-center text-xs font-medium text-primary hover:underline"
                                size="sm"
                                @click="showAuthorField = true">
                                <Icon
                                    name="Plus"
                                    class="size-3.5" />
                                Author
                            </button>
                        </div>
                        <div
                            v-if="showAuthorField"
                            class="grid gap-2">
                            <Label for="author">Author</Label>
                            <Input
                                id="author"
                                v-model.trim="author"
                                class="w-full"
                                name="author"
                                placeholder="e.g Peter Benchley" />
                        </div>
                    </div>
                    <div class="flex w-full items-center justify-center gap-2">
                        <Button
                            v-if="hasSearch"
                            as-child
                            variant="outline"
                            class="flex w-full flex-1">
                            <Link :href="useRoute('books.search')">
                                Reset
                            </Link>
                        </Button>
                        <Button
                            type="submit"
                            class="flex w-full flex-1">
                            Search
                        </Button>
                    </div>
                </form>

                <div>
                    <div class="my-3 flex items-center md:my-6">
                        <Separator class="flex flex-1" />
                        <span class="flex px-4 text-sm text-muted-foreground">or</span>
                        <Separator class="flex flex-1" />
                    </div>
                    <Dialog :default-open="scan">
                        <DialogTrigger as-child>
                            <Button
                                class="w-full cursor-pointer"
                                variant="secondary">
                                <Icon
                                    name="ScanBarcode"
                                    class="w-4" /> Scan barcode
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-lg">
                            <DialogTitle>Add via barcode</DialogTitle>
                            <div class="mt-2 overflow-auto max-h-[80dvh]">
                                <BarcodeScanner />
                            </div>
                        </DialogContent>
                    </Dialog>
                </div>
            </aside>
            <section class="flex flex-1 flex-col w-full md:w-auto">
                <div
                    v-if="hasSearch && results && results.total > 0"
                    class="mb-4 flex justify-between text-sm text-muted-foreground">
                    <p class="hidden md:flex">
                        Found {{ formatNumber(results.total) }} books
                    </p>
                    <p class="hidden md:flex">
                        Showing {{ formatNumber(results.books.length) }} results
                    </p>
                    <p class="md:hidden">
                        Showing {{ formatNumber(results.books.length) }} of {{ formatNumber(results.total) }} results
                    </p>
                </div>

                <div
                    v-if="!hasSearch"
                    class="mb-4 flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed px-4 py-8 md:py-16 text-center text-sm text-muted-foreground border-primary/10">
                    <Icon
                        name="Search"
                        class="size-8" />
                    <h3 class="font-serif text-2xl font-semibold">
                        Start searching
                    </h3>
                    <p>
                        Search for books by title or author, or scan a book's barcode to add it to your library.
                    </p>
                </div>

                <Deferred
                    v-else
                    data="results">
                    <template #fallback>
                        <div>
                            <div class="mt-1 mb-4 flex items-center justify-between">
                                <Skeleton class="h-4 w-32" />
                                <Skeleton class="h-4 w-36" />
                            </div>
                            <div class="relative">
                                <ul class="relative -mt-2 divide-y divide-muted-foreground/5">
                                    <li
                                        v-for="n in 3"
                                        :key="n">
                                        <HorizontalSkeleton />
                                    </li>
                                </ul>
                                <div class="absolute top-24 md:top-1/2 left-1/2 flex flex-col items-center gap-2 -translate-1/2">
                                    <Loader
                                        color="#913608"
                                        class="mx-auto w-10 md:w-18" />
                                    <p>
                                        Searching&hellip;
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div
                        v-if="results && results.total > 0"
                        class="-mt-2">
                        <ul
                            class="divide-y divide-muted-foreground/5">
                            <BookCardHorizontal
                                v-for="book in hasSearch ? results.books : []"
                                :key="book.identifier"
                                class="py-4"
                                :book="book" />
                        </ul>

                        <div v-if="loadingMore">
                            <HorizontalSkeleton />
                        </div>

                        <div
                            v-if="results.books.length < results.total"
                            class="mt-4 mb-36 flex items-center justify-center">
                            <Button
                                variant="secondary"
                                :disabled="loadingMore"
                                @click="loadMore">
                                <Icon
                                    v-if="!loadingMore"
                                    name="Plus"
                                    class="w-4" />
                                <Icon
                                    v-if="loadingMore"
                                    name="LoaderCircle"
                                    class="w-4 animate-spin" />
                                Load {{ perPage }} more
                            </Button>
                        </div>
                    </div>
                    <div
                        v-else
                        class="mb-4 flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed px-4 py-8 md:py-16 text-center text-sm text-muted-foreground border-primary/10">
                        <Icon
                            name="BookDashed"
                            class="size-8" />
                        <h3 class="font-serif text-2xl font-semibold">
                            No books found
                        </h3>
                        <p>
                            Try adjusting your search terms or use different keywords.
                        </p>
                    </div>
                </Deferred>
            </section>
        </div>
    </div>
</template>
