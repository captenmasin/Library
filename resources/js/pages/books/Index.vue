<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCard from '@/components/books/BookCard.vue'
import CheckboxList from '@/components/CheckboxList.vue'
import ShelfView from '@/components/books/ShelfView.vue'
import BookViewTabs from '@/components/books/BookViewTabs.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import type { Book } from '@/types/book'
import type { Author } from '@/types/author'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useUrlSearchParams } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useUserSettings } from '@/composables/useUserSettings'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { computed, ref, watch, type PropType, nextTick } from 'vue'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

/* --------------------------------------------------------------------------
 * Props & Refs
 * -------------------------------------------------------------------------- */
const props = defineProps({
    books: Array as PropType<Book[]>,
    selectedStatuses: { type: Array as PropType<string[]>, default: () => [] },
    selectedAuthor: { type: String as PropType<string | null>, default: null },
    selectedSort: { type: String, default: null },
    selectedOrder: { type: String, default: 'desc' },
    authors: { type: Array as PropType<Author[]>, default: () => [] }
})

const params = useUrlSearchParams('history')
const { possibleStatuses } = useUserBookStatus()

/** Search --------------------------------------------------------------- */
const searchInput = ref<HTMLInputElement | null>(null)
const search = ref(String(params.search || ''))
const currentSearch = ref(String(params.search || ''))
const displayFilters = ref(search.value !== '' || props.selectedStatuses.length > 0 || props.selectedAuthor !== null)

/** Filters -------------------------------------------------------------- */
const status = ref<string[]>(props.selectedStatuses)
const author = ref<string | null>(props.selectedAuthor)
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
    { label: 'Author', value: 'author' },
    { label: 'Rating', value: 'rating' },
    { label: 'Published', value: 'published_date' },
    { label: 'Colour', value: 'colour' }
] as const

/* --------------------------------------------------------------------------
 * Watchers
 * -------------------------------------------------------------------------- */
watch(
    [author, status, sort, order],
    () => {
        Object.assign(params, {
            author: author.value,
            status: status.value,
            sort: sort.value,
            order: order.value
        })
        submitForm()
    },
    { deep: true }
)

watch(view, (newView) => updateSingleSetting('library.view', newView))

/* --------------------------------------------------------------------------
 * Computed
 * -------------------------------------------------------------------------- */
const filteredBooks = computed(() => props.books ?? [])

const hasFiltered = computed(
    () => !!currentSearch.value || !!author.value || sort.value !== null || status.value.length > 0 || order.value !== 'desc'
)

/* --------------------------------------------------------------------------
 * Methods
 * -------------------------------------------------------------------------- */
function submitForm () {
    currentSearch.value = search.value

    nextTick(() => {
        searchInput.value?.blur()
    })

    router.get(
        useRoute('library.index'),
        {
            search: search.value,
            author: author.value,
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
        <div class="flex flex-col gap-2.5 md:gap-4 md:flex-row md:items-center">
            <div class="flex justify-between items-center gap-8">
                <PageTitle class="flex-1">
                    <template v-if="currentSearch">
                        Search results for "{{ currentSearch }}"
                    </template>
                    <template v-else>
                        All Books ({{ filteredBooks.length }})
                    </template>
                </PageTitle>
                <BookViewTabs
                    v-model="view"
                    class="flex md:hidden shrink-0 w-32 flex-1 max-w-32" />
            </div>

            <!-- View & Sort Controls ---------------------------------------- -->
            <div class="flex w-full flex-col items-center gap-2.5 md:gap-2 md:ml-auto md:w-auto md:flex-row">
                <!-- View toggle -->
                <BookViewTabs
                    v-model="view"
                    class="hidden md:flex" />

                <!-- Sort dropdown & order -->
                <div class="flex w-full items-center justify-end gap-2.5 md:gap-2 md:w-56">
                    <Select v-model="sort">
                        <SelectTrigger class="w-full">
                            <span
                                v-if="sort"
                                class="text-muted-foreground">Sort:</span>
                            <SelectValue placeholder="Sort by..." />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="opt in sortOptions"
                                    :key="opt.value"
                                    :value="opt.value">
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>

                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger>
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="cursor-pointer bg-white text-secondary-foreground"
                                    size="icon"
                                    @click="order = order === 'asc' ? 'desc' : 'asc'"
                                >
                                    <Icon :name="order === 'asc' ? 'ArrowUpWideNarrow' : 'ArrowDownWideNarrow'" />
                                </Button>
                            </TooltipTrigger>
                            <TooltipContent>
                                <span>
                                    {{ order === 'asc' ? 'Ascending' : 'Descending' }}
                                </span>
                            </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>
                    <Button
                        class="cursor-pointer bg-white text-secondary-foreground md:hidden"
                        variant="outline"
                        size="icon"
                        @pointerup="displayFilters = !displayFilters"
                    >
                        <Icon
                            name="Filter"
                            class="w-4" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Main layout ----------------------------------------------------- -->
        <div class="mt-2 md:mt-8 flex flex-col items-start gap-4 md:flex-row">
            <aside
                :class="displayFilters ? 'h-[calc-size(auto,size)] overflow-auto border-secondary' : 'h-0 overflow-hidden border-background'"
                class="w-[calc(100%+calc(var(--spacing)*8))] transition-[height,border-color] md:border-0 md:overflow-visible relative duration-500 px-4 md:px-0 border-y flex-col md:h-auto gap-2 md:flex md:w-64 bg-muted md:bg-transparent -mx-4 md:mx-0">
                <!-- Search ---------------------------------------------------- -->
                <div class="flex flex-col gap-2 mt-4 md:mt-0">
                    <form
                        class="flex w-full"
                        @submit.prevent="submitForm">
                        <div class="relative flex w-full">
                            <Input
                                ref="searchInput"
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

                    <!-- Author filter -------------------------------------------- -->
                    <Select
                        v-model="author">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Filter by author" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem :value="null">
                                    All Authors
                                </SelectItem>
                                <template v-if="authors.length">
                                    <SelectItem
                                        v-for="a in authors"
                                        :key="a.uuid"
                                        :value="a.uuid">
                                        {{ a.name }}
                                    </SelectItem>
                                </template>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>

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
                    class="w-full mt-4 mb-4"
                    as-child
                    variant="outline">
                    <Link
                        :href="useRoute('library.index')"
                        preserve-scroll>
                        Reset
                    </Link>
                </Button>
            </aside>

            <!-- Books list -------------------------------------------------- -->
            <section class="flex flex-1 mt-4 md:mt-0 flex-col">
                <div
                    class="flex items-center justify-center rounded-lg border-2 border-dashed py-16 px-4 gap-2 flex-col text-sm text-center text-muted-foreground border-primary/10">
                    <Icon
                        name="BookDashed"
                        class="size-8" />
                    <h3 class="font-semibold text-2xl font-serif">
                        duhhhhhhh...
                    </h3>
                    <p>
                        Please wait while we import the book. This may take a few minutes depending on the size of the book.
                    </p>
                </div>

                <ShelfView
                    v-if="view === 'shelf'"
                    :books="filteredBooks" />

                <ul
                    v-else
                    :class="view === 'list' ? 'grid-cols-1 gap-8 md:gap-4' : 'grid-cols-2 gap-6 md:grid-cols-5'"
                    class="grid md:gap-4">
                    <li
                        v-for="book in filteredBooks"
                        :key="book.identifier"
                        :class="view === 'list' ? 'flex gap-4' : ''"
                        class="w-full">
                        <BookCardHorizontal
                            v-if="view === 'list'"
                            :book="book" />
                        <BookCard
                            v-if="view === 'grid'"
                            :book="book" />
                    </li>
                </ul>
            </section>
        </div>
    </div>
</template>
