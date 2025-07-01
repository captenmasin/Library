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
            class="flex flex-col overflow-hidden rounded-md shadow-sm group"
            :style="{
                backgroundColor: book.colour,
            }"
            :class="[
                useContrast(book.colour, 'text-zinc-900', 'text-white'),
                horizontal ? 'w-24' : 'w-full',
            ]"
        >
            <Link
                :href="book.links.show"
                prefetch>
                <div class="relative w-full overflow-hidden aspect-book group">
                    <Image
                        v-if="book.cover"
                        :src="book.cover"
                        :height="315"
                        :width="200"
                        class="h-full w-full object-cover" />
                    <div
                        v-if="!horizontal"
                        class="absolute bottom-0 left-0 flex w-full items-end p-4 opacity-0 transition-all duration-300 h-11/12 group-hover:opacity-100"
                        :style="{backgroundImage: `linear-gradient(to top, ${changeColourOpacity(book.colour, 1)}, rgba(0, 0, 0, 0))`}">
                        <div class="flex w-full translate-y-4 flex-col opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                            <h2 class="font-serif font-semibold text-lg/6 line-clamp-5">
                                {{ book.title }}
                            </h2>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
        <div
            v-if="horizontal"
            class="flex flex-1 flex-col">
            <Link
                :href="book.links.show"
                prefetch
                class="transition-colors hover:text-primary">
                <h2 class="font-serif text-2xl font-bold">
                    {{ book.title }}
                </h2>
            </Link>
            <p class="text-xs text-gray-900/50 line-clamp-3">
                {{ book.authors.map(a => a.name).join(', ') }}
            </p>
            <p class="mt-1 text-sm line-clamp-3">
                {{ book.description_clean }}
            </p>
        </div>
    </article>
</template>
