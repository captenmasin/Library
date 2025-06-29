<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import BookCard from '@/components/books/BookCard.vue'
import CheckboxList from '@/components/CheckboxList.vue'
import ShelfView from '@/components/books/ShelfView.vue'
import BarcodeScanner from '@/components/books/BarcodeScanner.vue'
import type { Book } from '@/types/book'
import type { Author } from '@/types/author'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useUrlSearchParams } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref, watch, type PropType } from 'vue'
import { useUserSettings } from '@/composables/useUserSettings'
import { Tabs, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue
} from '@/components/ui/select'
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogTitle,
    DialogTrigger
} from '@/components/ui/dialog'

/* --------------------------------------------------------------------------
 * Props & Refs
 * -------------------------------------------------------------------------- */
const props = defineProps({
    books: Array as PropType<Book[]>,
    selectedStatuses: { type: Array as PropType<string[]>, default: () => [] },
    selectedAuthor: { type: String as PropType<string | null>, default: null },
    selectedPublisher: { type: String as PropType<string | null>, default: null },
    selectedSort: { type: String, default: 'added' },
    selectedOrder: { type: String, default: 'desc' },
    authors: { type: Array as PropType<Author[]>, default: () => [] },
    publishers: { type: Array as PropType<Author[]>, default: () => [] }
})

const params = useUrlSearchParams<'history'>('history')
const { possibleStatuses } = useUserBookStatus()

/** Search --------------------------------------------------------------- */
const search = ref((params.search as string) || '')
const currentSearch = ref((params.search as string) || '')

/** Filters -------------------------------------------------------------- */
const status = ref<string[]>(props.selectedStatuses)
const author = ref<string | null>(props.selectedAuthor)
const publisher = ref<string | null>(props.selectedPublisher)
const sort = ref(props.selectedSort)
const order = ref<'asc' | 'desc'>(props.selectedOrder as 'asc' | 'desc')

/** View preferences ----------------------------------------------------- */
const page = usePage()
const view = ref<string>(page.props.auth.user.settings?.library.view ?? 'list')
const { updateSingleSetting } = useUserSettings()

/** Options -------------------------------------------------------------- */
const sortOptions = [
    { label: 'Added', value: 'added' },
    { label: 'Title', value: 'title' },
    { label: 'Rating', value: 'rating' },
    { label: 'Published Date', value: 'published_date' },
    { label: 'Colour', value: 'colour' }
] as const

/* --------------------------------------------------------------------------
 * Watchers
 * -------------------------------------------------------------------------- */
watch(
    [author, publisher, status, sort, order],
    () => {
        Object.assign(params, {
            author: author.value,
            publisher: publisher.value,
            status: status.value,
            sort: sort.value,
            order: order.value
        })
        submitForm()
    },
    { deep: true }
)

watch(view, newView => updateSingleSetting('library.view', newView))

/* --------------------------------------------------------------------------
 * Computed
 * -------------------------------------------------------------------------- */
const filteredBooks = computed(() => props.books ?? [])

const hasFiltered = computed(
    () =>
        !!currentSearch.value ||
        !!author.value ||
        !!publisher.value ||
        sort.value !== 'added' ||
        status.value.length > 0 ||
        order.value !== 'desc'
)

/* --------------------------------------------------------------------------
 * Methods
 * -------------------------------------------------------------------------- */
function submitForm () {
    currentSearch.value = search.value

    router.get(
        useRoute('library.index'),
        {
            search: search.value,
            author: author.value,
            publisher: publisher.value,
            status: status.value,
            sort: sort.value,
            order: order.value
        },
        { preserveScroll: true, preserveState: true, replace: true }
    )
}

defineOptions({ layout: AppLayout })
</script>

<template>
    <div class="container mx-auto">
        <!-- Header --------------------------------------------------------- -->
        <div class="flex items-center gap-4">
            <h2 class="font-serif text-3xl font-bold">
                <template v-if="currentSearch">
                    Search results for "{{ currentSearch }}"
                </template>
                <template v-else>
                    All Books ({{ filteredBooks.length }})
                </template>
            </h2>

            <!-- View & Sort Controls ---------------------------------------- -->
            <div class="ml-auto flex items-center gap-2">
                <!-- View toggle -->
                <Tabs
                    v-model="view"
                    :default-value="view">
                    <TabsList>
                        <TabsTrigger
                            class="px-4"
                            value="list">
                            <Icon
                                name="LayoutList"
                                class="w-4" /> List
                        </TabsTrigger>
                        <TabsTrigger
                            class="px-4"
                            value="grid">
                            <Icon
                                name="LayoutGrid"
                                class="w-4" /> Grid
                        </TabsTrigger>
                        <TabsTrigger
                            class="px-4"
                            value="shelf">
                            <Icon
                                name="LayoutGrid"
                                class="w-4" /> Shelf
                        </TabsTrigger>
                    </TabsList>
                </Tabs>

                <!-- Sort dropdown & order -->
                <div class="flex w-48 items-center justify-end gap-2">
                    <Select v-model="sort">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Sort books" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="opt in sortOptions"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>

                    <Button
                        type="button"
                        variant="outline"
                        class="cursor-pointer bg-white text-secondary-foreground"
                        size="icon"
                        @click="order = order === 'asc' ? 'desc' : 'asc'"
                    >
                        <Icon :name="order === 'asc' ? 'ArrowUpWideNarrow' : 'ArrowDownWideNarrow'" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Main layout ----------------------------------------------------- -->
        <div class="mt-8 flex items-start gap-4">
            <!-- Sidebar filters -->
            <aside class="flex w-64 flex-col gap-2">
                <!-- Search ---------------------------------------------------- -->
                <form @submit.prevent="submitForm">
                    <div class="relative flex">
                        <Input
                            v-model="search"
                            class="pr-10"
                            placeholder="Search" />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-1">
                            <Button
                                type="submit"
                                variant="link"
                                class="cursor-pointer"
                                size="icon"
                            >
                                <Icon name="Search" />
                            </Button>
                        </div>
                    </div>
                </form>

                <!-- Author filter -------------------------------------------- -->
                <Select
                    v-if="authors.length"
                    v-model="author">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Filter by author" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem :value="null">
                                All Authors
                            </SelectItem>
                            <SelectItem
                                v-for="a in authors"
                                :key="a.uuid"
                                :value="a.uuid"
                            >
                                {{ a.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <!-- Publisher filter ----------------------------------------- -->
                <Select
                    v-if="publishers.length"
                    v-model="publisher">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Filter by publisher" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem :value="null">
                                All Publishers
                            </SelectItem>
                            <SelectItem
                                v-for="p in publishers"
                                :key="p.uuid"
                                :value="p.uuid"
                            >
                                {{ p.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>

                <!-- Status filter -------------------------------------------- -->
                <div class="mt-4">
                    <p class="mb-2 font-serif">
                        Filter by status
                    </p>
                    <CheckboxList
                        v-model="status"
                        :options="possibleStatuses" />
                </div>

                <!-- Reset button -------------------------------------------- -->
                <Button
                    v-if="hasFiltered"
                    class="w-full"
                    as-child
                    variant="secondary"
                >
                    <Link
                        :href="useRoute('library.index')"
                        preserve-scroll>
                        Reset
                    </Link>
                </Button>
            </aside>

            <!-- Books list -------------------------------------------------- -->
            <section class="flex flex-1 flex-col">
                <ShelfView
                    v-if="view === 'shelf'"
                    :books="filteredBooks"
                />

                <ul
                    v-else
                    :class="
                        view === 'list'
                            ? 'grid-cols-1'
                            : 'grid-cols-2 md:grid-cols-5'
                    "
                    class="grid gap-4"
                >
                    <li
                        v-for="book in filteredBooks"
                        :key="book.identifier"
                        :class="view === 'list' ? 'flex gap-4' : ''"
                        class="w-full"
                    >
                        <BookCard
                            :horizontal="view === 'list'"
                            :book="book" />
                    </li>
                </ul>
            </section>
        </div>
    </div>
</template>
