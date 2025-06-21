<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import BookCard from '@/components/books/BookCard.vue'
import BookForm from '@/components/books/BookForm.vue'
import BookSearch from '@/components/books/BookSearch.vue'
import { Author } from '@/types/author'
import type { Book } from '@/types/book'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useUrlSearchParams } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { computed, type PropType, ref, watch } from 'vue'
import { Head, Link, router, WhenVisible } from '@inertiajs/vue3'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    books: Object as PropType<Book[]>,
    authors: {
        type: Array as PropType<Author[]>,
        default: () => []
    }
})

const params = useUrlSearchParams('history')

const search = ref<string>((params.search as string) || '')
const author = ref<string>((params.author as string) || '')
const sort = ref<string>((params.sort as string) || 'title')
const order = ref<string>((params.order as string) || 'asc')

const sortOptions = ref([
    { label: 'Title', value: 'title' },
    { label: 'Rating', value: 'rating' },
    { label: 'Published Date', value: 'published_date' }
])

watch([search, author, sort, order], ([newSearch, newAuthor, newSort, newOrder]) => {
    params.search = newSearch
    params.author = newAuthor
    params.sort = newSort
    params.order = newOrder

    router.get(
        useRoute('books.index'),
        {
            search: newSearch,
            author: newAuthor,
            sort: newSort,
            order: newOrder
        },
        {
            preserveScroll: true,
            replace: true
        }
    )
})

const filteredBooks = computed(() => {
    if (!props.books) return []

    return props.books
})

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <Head title="Books" />

    <div class="container mx-auto">
        <BookSearch />

        <div class="flex gap-4">
            <div class="flex w-3/12 flex-col bg-red-200">
                <div class="w-full">
                    <Input
                        v-model="search"
                        placeholder="Search" />
                </div>
                <div class="w-full">
                    <Select v-model="author">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Select author" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem :value="null">
                                    All
                                </SelectItem>
                                <SelectItem
                                    v-for="singleAuthor in authors"
                                    :key="singleAuthor.uuid"
                                    :value="singleAuthor.uuid">
                                    {{ singleAuthor.name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div class="w-full">
                    <Select v-model="sort">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Sort books" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="sortOption in sortOptions"
                                    :key="sortOption.value"
                                    :value="sortOption.value">
                                    {{ sortOption.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
                <div class="w-full">
                    <div>
                        <Button
                            type="button"
                            variant="outline"
                            size="icon"
                            @click="order = order === 'asc' ? 'desc' : 'asc'">
                            <Icon :name="order === 'asc' ? 'ArrowUpWideNarrow' : 'ArrowDownWideNarrow'" />
                        </Button>
                    </div>
                    <div>
                        <Button
                            as-child
                            variant="outline">
                            <Link
                                :href="useRoute('books.index')"
                                preserve-scroll>
                                Reset
                            </Link>
                        </Button>
                    </div>
                </div>
            </div>

            <div class="flex w-9/12">
                <ul class="w-full flex flex-wrap items-stretch">
                    <li
                        v-for="book in filteredBooks"
                        :key="book.identifier"
                        class="flex w-1/2 flex-col p-2 sm:w-1/2 md:w-1/5">
                        <BookCard :book="book" />
                    </li>
                </ul>
            <!--            <WhenVisible data="books">-->
            <!--                <template #fallback>-->
            <!--                    <Loader class="scale-50" />-->
            <!--                </template>-->

            <!--                <ul class="-mx-2 flex flex-wrap items-stretch">-->
            <!--                    <li-->
            <!--                        v-for="book in filteredBooks"-->
            <!--                        :key="book.id"-->
            <!--                        class="flex h-full w-1/2 flex-col p-2 sm:w-1/2 md:w-1/6"-->
            <!--                    >-->
            <!--                        <BookCard :book="book" />-->
            <!--                    </li>-->
            <!--                </ul>-->
            <!--            </WhenVisible>-->
            </div>
        </div>
    </div>
</template>
