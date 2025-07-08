<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import BookCard from '@/components/books/BookCard.vue'
import { PropType } from 'vue'
import { Book } from '@/types/book'
import { useTimeAgo } from '@vueuse/core'
import { Activity } from '@/types/activity'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

type Stats = {
    booksInLibrary: number
    completedBooks: number
    planToRead: number
}

defineProps({
    activities: {
        type: Array as PropType<Activity[]>,
        default: () => []
    },
    currentlyReading: {
        type: Array as PropType<Book[]>,
        default: () => []
    },
    stats: {
        type: Object as PropType<Stats>,
        default: () => ({})
    }
})

function getTimeAgo (date) {
    return useTimeAgo(new Date(date))
}

defineOptions({ layout: AppLayout })
</script>

<template>
    <div>
        <header class="mb-10">
            <h1 class="text-3xl font-bold font-serif text-gray-800">
                Welcome back, Mason
            </h1>
            <p class="text-sm text-gray-500">
                Here's a quick look at your library
            </p>
        </header>

        <section class="mb-12">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">
                Your reading summary
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="rounded-xl p-4 bg-white shadow flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            Books in library
                        </p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ stats.booksInLibrary || 0 }}
                        </p>
                    </div>
                    <svg
                        class="w-6 h-6 text-gray-300"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4" />
                    </svg>
                </div>

                <div class="rounded-xl p-4 bg-white shadow flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            Completed
                        </p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ stats.completedBooks || 0 }}
                        </p>
                    </div>
                    <svg
                        class="w-6 h-6 text-green-100"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div class="rounded-xl p-4 bg-white shadow flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">
                            Plan to read
                        </p>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ stats.planToRead || 0 }}
                        </p>
                    </div>
                    <svg
                        class="w-6 h-6 text-blue-100"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6l4 2" />
                    </svg>
                </div>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">
                Continue reading
            </h2>

            <ul
                class="grid grid-cols-2 gap-6 md:grid-cols-6 md:gap-4">
                <li>
                    <BookCard
                        v-for="book in currentlyReading"
                        :key="book.identifier"
                        :book="book" />
                </li>
            </ul>
        </section>

        <section class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-700">
                    Recent activity
                </h2>
                <a
                    href="#"
                    class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            <ul class="bg-white rounded-xl shadow divide-y divide-muted">
                <li
                    v-for="activity in activities"
                    :key="activity.id"
                    class="p-4 text-sm flex justify-between items-center">
                    <p
                        class="text-secondary-foreground"
                        v-html="activity.description" />
                    <div class="flex shrink-0">
                        <TooltipProvider>
                            <Tooltip>
                                <TooltipTrigger>
                                    <p class="text-secondary-foreground/50">
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
</template>
