<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BarcodeScanner from '@/components/books/BarcodeScanner.vue'
import HorizontalSkeleton from '@/components/books/HorizontalSkeleton.vue'
import SingleSearchResult from '@/components/books/SingleSearchResult.vue'
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

        <div class="mt-4 md:mt-8 flex flex-col md:flex-row items-start gap-8">
            <aside class="md:sticky top-4 left-0 w-full md:w-64">
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

                        <div class="grid gap-2">
                            <Label for="author">Author</Label>
                            <Input
                                id="author"
                                v-model.trim="author"
                                class="w-full"
                                name="author"
                                placeholder="e.g Peter Benchley" />
                        </div>
                    </div>
                    <div class="flex items-center justify-center gap-2 w-full">
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
                    <div class="flex items-center my-3 md:my-6">
                        <Separator class="flex flex-1" />
                        <span class="flex px-4 text-sm text-muted-foreground">or</span>
                        <Separator class="flex flex-1" />
                    </div>
                    <Dialog>
                        <DialogTrigger as-child>
                            <Button
                                class="cursor-pointer w-full"
                                variant="secondary">
                                <Icon
                                    name="ScanBarcode"
                                    class="w-4" /> Scan barcode
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-lg">
                            <DialogTitle>Add via barcode</DialogTitle>
                            <div class="max-h-[80dvh] mt-2 overflow-auto">
                                <BarcodeScanner />
                            </div>
                        </DialogContent>
                    </Dialog>
                </div>
            </aside>
            <section class="flex flex-1 flex-col">
                <div
                    v-if="hasSearch && results && results.total > 0"
                    class="flex justify-between mb-4 text-sm text-muted-foreground">
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
                    class="mb-4 flex items-center justify-center rounded-lg border-2 border-dashed py-16 px-4 gap-2 flex-col text-sm text-center text-muted-foreground border-primary/10">
                    <Icon
                        name="Search"
                        class="size-8" />
                    <h3 class="font-semibold text-2xl font-serif">
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
                            <div class="flex mt-1 mb-4 items-center justify-between">
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
                                <div class="absolute top-1/2 left-1/2 flex flex-col items-center gap-2 -translate-1/2">
                                    <Loader
                                        color="#913608"
                                        class="mx-auto w-18" />
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
                            <SingleSearchResult
                                v-for="book in hasSearch ? results.books : []"
                                :key="book.identifier"
                                class="py-2"
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
                        class="mb-4 flex items-center justify-center rounded-lg border-2 border-dashed py-16 px-4 gap-2 flex-col text-sm text-center text-muted-foreground border-primary/10">
                        <Icon
                            name="BookOpen"
                            class="size-8" />
                        <h3 class="font-semibold text-2xl font-serif">
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
