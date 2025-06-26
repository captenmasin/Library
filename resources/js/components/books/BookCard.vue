<script setup lang="ts">
import Image from '@/components/Image.vue'
import type { PropType } from 'vue'
import { Link } from '@inertiajs/vue3'
import type { Book } from '@/types/book'
import { useRoute } from '@/composables/useRoute'
import { useColours } from '@/composables/useColours'
import { useContrast } from '@/composables/useContrast'

defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    horizontal: {
        type: Boolean,
        default: false
    }
})

const { changeColourOpacity } = useColours()
</script>

<template>
    <article class="flex gap-4">
        <div
            class="group flex flex-col overflow-hidden rounded-md shadow-sm"
            :style="{
                backgroundColor: book.colour,
                viewTransitionName: `book-cover-${book.identifier}`,
            }"
            :class="[
                useContrast(book.colour, 'text-zinc-900', 'text-white'),
                horizontal ? 'w-24' : 'w-full',
            ]"
        >
            <Link
                :href="book.links.show"
                prefetch>
                <div class="relative aspect-book w-full overflow-hidden group">
                    <Image
                        v-if="book.cover"
                        :src="book.cover"
                        :height="315"
                        :width="200"
                        class="h-full w-full object-cover" />
                    <div
                        v-if="!horizontal"
                        class="absolute bottom-0 h-11/12 opacity-0 duration-300 transition-all group-hover:opacity-100 flex items-end left-0 w-full p-4"
                        :style="{backgroundImage: `linear-gradient(to top, ${changeColourOpacity(book.colour, 1)}, rgba(0, 0, 0, 0))`}">
                        <div class="flex flex-col w-full translate-y-4 opacity-0 duration-300 group-hover:opacity-100 group-hover:translate-y-0 transition-all">
                            <h2 class="font-serif text-lg/6 font-semibold line-clamp-5">
                                {{ book.title }}
                            </h2>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
        <div
            v-if="horizontal"
            class="flex flex-col flex-1">
            <Link
                :href="book.links.show"
                prefetch
                class="hover:text-primary transition-colors">
                <h2 class="font-bold font-serif text-2xl">
                    {{ book.title }}
                </h2>
            </Link>
            <p class="line-clamp-3 text-xs text-gray-900/50">
                {{ book.authors.map(a => a.name).join(', ') }}
            </p>
            <p class="line-clamp-3 text-sm mt-1">
                {{ book.description_clean }}
            </p>
        </div>
    </article>
</template>
