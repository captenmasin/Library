<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { PropType } from 'vue'
import { Activity } from '@/types/activity'

defineProps({
    activities: {
        type: Array as PropType<Activity[]>,
        default: () => []
    }
})

defineOptions({ layout: AppLayout })
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-10 px-6 sm:px-10">
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
                            42
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
                            17
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
                            12
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
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <div class="flex items-start gap-4 bg-white p-4 rounded-lg shadow">
                    <img
                        src="/placeholder-cover.png"
                        alt="Book cover"
                        class="w-16 h-24 object-cover rounded"
                    >
                    <div class="flex-1">
                        <h3 class="text-md font-semibold">
                            The Way of Kings
                        </h3>
                        <p class="text-sm text-gray-500">
                            Brandon Sanderson
                        </p>
                        <p class="mt-2 text-xs text-gray-400">
                            Last read: 3 days ago
                        </p>
                    </div>
                </div>
                <!-- Repeat for other books -->
            </div>
        </section>

        <ul>
            <li
                v-for="activity in activities"
                :key="activity.id"
                class="text-sm text-muted-foreground py-4 border-b"
            >
                <h2>{{ activity.id }}</h2>
                <p v-html="activity.description" />
                <!--                <small>{{ activity.created_at }}</small>-->
            </li>
        </ul>

        <section class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-700">
                    Recent activity
                </h2>
                <a
                    href="#"
                    class="text-sm text-blue-600 hover:underline">View all</a>
            </div>
            <ul class="bg-white rounded-xl shadow divide-y divide-gray-100">
                <li class="p-4 text-sm text-gray-700">
                    You added <strong>1984</strong> to your library.
                </li>
                <li class="p-4 text-sm text-gray-700">
                    You marked <strong>Dune</strong> as completed.
                </li>
                <li class="p-4 text-sm text-gray-700">
                    You reviewed <strong>Project Hail Mary</strong>.
                </li>
            </ul>
        </section>
    </div>
</template>
