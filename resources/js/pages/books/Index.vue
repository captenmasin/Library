<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import BookCard from '@/components/books/BookCard.vue'
import BookSearch from '@/components/books/BookSearch.vue'
import BarcodeScanner from '@/components/books/BarcodeScanner.vue'
import { Author } from '@/types/author'
import type { Book } from '@/types/book'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useUrlSearchParams } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { computed, type PropType, ref, watch } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { useUserSettings } from '@/composables/useUserSettings'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    books: Object as PropType<Book[]>,
    authors: {
        type: Array as PropType<Author[]>,
        default: () => []
    },
    publishers: {
        type: Array as PropType<Author[]>,
        default: () => []
    }
})

const params = useUrlSearchParams('history')

const { possibleStatuses } = useUserBookStatus()

const search = ref<string>((params.search as string) || '')
const currentSearch = ref<string>((params.search as string) || '')

const status = ref<string>((params.status as string) || '')
const author = ref<string>((params.author as string) || '')
const publisher = ref<string>((params.publisher as string) || '')
const sort = ref<string>((params.sort as string) || '')
const order = ref<string>((params.order as string) || 'desc')

const page = usePage()

const view = ref<string>(page.props.auth.user.settings.books.view)

const sortOptions = ref([
    { label: 'Title', value: 'title' },
    { label: 'Rating', value: 'rating' },
    { label: 'Published Date', value: 'published_date' }
])

watch([author, publisher, status, sort, order], ([newAuthor, newPublisher, newStatus, newSort, newOrder]) => {
    params.author = newAuthor
    params.publisher = newPublisher
    params.status = newStatus
    params.sort = newSort
    params.order = newOrder

    submitForm()
})

const { updateSingleSettings } = useUserSettings()

function setView (newView: string) {
    view.value = newView

    updateSingleSettings('books.view', newView)
}

function submitForm () {
    currentSearch.value = search.value
    router.get(
        useRoute('books.index'),
        {
            search: search.value,
            author: author.value,
            publisher: publisher.value,
            status: status.value,
            sort: sort.value,
            order: order.value
        },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true
        }
    )
}

const filteredBooks = computed(() => {
    if (!props.books) return []

    return props.books
})

const hasFiltered = computed(() => {
    return currentSearch.value !== '' ||
        author.value !== '' ||
        publisher.value !== '' ||
        sort.value !== '' ||
        status.value !== '' ||
        order.value !== 'desc'
})

defineOptions({
    layout: AppLayout
})
</script>

<template>
    <Head title="Books" />

    <div class="container mx-auto">
        <BookSearch />
        <BarcodeScanner class="w-48" />

        <div class="flex gap-4">
            <div class="flex w-3/12 flex-col gap-2">
                <div class="w-full">
                    <form @submit.prevent="submitForm">
                        <div class="flex relative">
                            <Input
                                v-model="search"
                                class="pr-10"
                                placeholder="Search" />
                            <div class="absolute inset-y-0 right-0 flex items-center pr-1">
                                <Button
                                    type="submit"
                                    variant="link"
                                    class="cursor-pointer"
                                    size="icon">
                                    <Icon name="Search" />
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="w-full">
                    <Select v-model="author">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter by author" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
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
                    <Select v-model="publisher">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter by publisher" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="singlePublisher in publishers"
                                    :key="singlePublisher.uuid"
                                    :value="singlePublisher.uuid">
                                    {{ singlePublisher.name }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <div class="w-full">
                    <Select v-model="status">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter by status" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="singleStatus in possibleStatuses"
                                    :key="singleStatus.value"
                                    :value="singleStatus.value">
                                    {{ singleStatus.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

                <div
                    v-if="hasFiltered"
                    class="w-full">
                    <Button
                        class="w-full"
                        as-child
                        variant="secondary">
                        <Link
                            :href="useRoute('books.index')"
                            preserve-scroll>
                            Reset
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="flex w-9/12 flex-col">
                <div class="flex items-center">
                    <h2
                        v-if="currentSearch"
                        class="font-semibold text-2xl mb-4">
                        Search results for "{{ currentSearch }}"
                    </h2>
                    <div class="w-full flex items-center gap-2">
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
                            :variant="view === 'grid' ? 'default' : 'secondary'"
                            @click="setView('grid')">
                            Grid
                        </Button>
                        <Button
                            :variant="view === 'list' ? 'default' : 'secondary'"
                            @click="setView('list')">
                            List
                        </Button>
                    </div>
                </div>
                <ul
                    :class="view === 'list' ? 'flex flex-col gap-y-4' : ''"
                    class="w-full flex gap-y-4 flex-wrap items-stretch">
                    <li
                        v-for="book in filteredBooks"
                        :key="book.identifier"
                        class="flex w-1/2 px-2 flex-col sm:w-1/2 md:w-1/5">
                        <BookCard :book="book" />
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
