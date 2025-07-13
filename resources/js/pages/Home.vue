<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import TagCloud from '@/components/TagCloud.vue'
import BookCard from '@/components/books/BookCard.vue'
import { PropType } from 'vue'
import { Tag } from '@/types/tag'
import { Book } from '@/types/book'
import { Link } from '@inertiajs/vue3'
import { Author } from '@/types/author'
import { useTimeAgo } from '@vueuse/core'
import { Activity } from '@/types/activity'
import { Label } from '@/components/ui/label'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

type Stats = {
    booksInLibrary: number;
    completedBooks: number;
    readingBooks: number;
    pagesRead: number | string;
};

const props = defineProps({
    activities: {
        type: Array as PropType<Activity[]>,
        default: () => []
    },
    currentlyReading: {
        type: Array as PropType<Book[]>,
        default: () => []
    },
    statValues: {
        type: Object as PropType<Stats>,
        default: () => ({})
    },
    tags: {
        type: Array as PropType<Tag[]>,
        default: () => []
    },
    authors: {
        type: Array as PropType<Author[]>,
        default: () => []
    }
})

const actions = [
    { name: 'View all books', icon: 'book-open', url: 'viewAllBooks' },
    { name: 'Find a new book', icon: 'plus', url: 'addBook' },
    { name: 'Scan a barcode', icon: 'plus', url: 'addBook' }
]

const stats = [
    { name: 'Books in library', value: props.statValues.booksInLibrary, icon: 'LibraryBig', color: 'text-primary' },
    { name: 'Completed', value: props.statValues.completedBooks, icon: 'CircleCheck', color: 'text-green-500' },
    { name: 'Reading', value: props.statValues.readingBooks, icon: 'BookOpen', color: 'text-yellow-500' },
    { name: 'Pages read this year', value: props.statValues.pagesRead, icon: 'Hash', color: 'text-blue-500' }
]

function getTimeAgo (date: string | Date) {
    return useTimeAgo(new Date(date))
}

defineOptions({ layout: AppLayout })
</script>

<template>
    <div class="container">
        <header class="mb-4 mt-6">
            <h1 class="font-serif text-3xl font-bold text-foreground">
                Welcome back, Mason
            </h1>
            <p class="text-sm text-accent-foreground">
                Here's a quick look at your library
            </p>
        </header>

        <section class="mb-12">
            <!--            <h2 class="mb-4 text-xl font-semibold text-gray-700">-->
            <!--                Your reading summary-->
            <!--            </h2>-->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="stat in stats"
                    :key="stat.name"
                    class="flex items-center justify-between rounded-xl bg-white p-4 shadow">
                    <div>
                        <p class="text-sm text-current/60">
                            {{ stat.name }}
                        </p>
                        <p class="text-2xl font-bold">
                            {{ stat.value }}
                        </p>
                    </div>
                    <Icon
                        v-if="stat.icon"
                        :name="stat.icon"
                        :class="stat.color"
                        class="size-6"
                    />
                </div>
            </div>
        </section>

        <div class="mt-2 flex flex-col items-start gap-8 md:mt-8 md:flex-row">
            <div class="mt-4 flex flex-1 flex-col md:mt-0">
                <section class="mb-12 hidden">
                    <ul class="flex gap-4 w-full">
                        <li
                            v-for="action in actions"
                            :key="action.name">
                            <Button
                                variant="outline"
                                class="cursor-pointer"
                                :href="action.url">
                                {{ action.name }}
                            </Button>
                        </li>
                    </ul>
                </section>

                <section class="mb-12">
                    <h2 class="mb-4 font-serif text-xl font-semibold text-gray-700">
                        Currently reading
                    </h2>

                    <ul class="grid w-full gap-6 md:grid-cols-5 md:gap-4">
                        <li
                            v-for="book in currentlyReading"
                            :key="book.identifier">
                            <BookCard :book="book" />
                        </li>
                        <li>
                            <Link
                                :href="useRoute('books.search')"
                                class="size-full border-3 p-4 text-center text-primary/50 hover:bg-primary/10 bg-primary/5 text-sm border-primary/20 border-dashed rounded-md flex items-center justify-center ">
                                Find more books
                            </Link>
                        </li>
                    </ul>
                </section>

                <section class="mb-12">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="font-serif text-xl font-semibold text-gray-700">
                            Recent activity
                        </h2>
                        <Button
                            as-child
                            class="px-0"
                            variant="link">
                            <Link :href="'#'">
                                View all
                            </Link>
                        </Button>
                    </div>
                    <ul class="divide-y divide-muted rounded-xl bg-white shadow">
                        <li
                            v-for="activity in activities"
                            :key="activity.id"
                            class="flex flex-col gap-1 justify-between p-4 text-sm">
                            <p
                                class="text-secondary-foreground"
                                v-html="activity.description" />
                            <div class="flex shrink-0">
                                <TooltipProvider>
                                    <Tooltip>
                                        <TooltipTrigger>
                                            <p class="text-secondary-foreground/50 text-xs">
                                                {{ getTimeAgo(activity.created_at) }}
                                            </p>
                                        </TooltipTrigger>
                                        <TooltipContent>
                                            <p>
                                                {{ new Date(activity.created_at).toLocaleString() }}
                                            </p>
                                        </TooltipContent>
                                    </Tooltip>
                                </TooltipProvider>
                            </div>
                        </li>
                    </ul>
                </section>
            </div>
            <div class="w-72">
                <div>
                    <h2 class="mb-2 font-serif text-xl font-semibold text-gray-700">
                        Top tags
                    </h2>
                    <TagCloud
                        :tags
                        :limit="10" />
                </div>
                <div class="my-8">
                    <h2 class="mb-2 font-serif text-xl font-semibold text-gray-700">
                        Top authors
                    </h2>
                    <ul class="mt-2 divide-y divide-muted rounded-xl bg-white p-4 shadow">
                        <li
                            v-for="author in authors"
                            :key="author.uuid"
                            class="flex items-center py-2 gap-2">
                            <span class="text-sm text-gray-700">
                                {{ author.name }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
