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
    }
})

const { changeColourOpacity } = useColours()
</script>

<template>
    <article class="flex gap-4">
        <div
            class="flex flex-col w-full overflow-hidden rounded-md shadow-sm group"
            :style="{
                backgroundColor: book.colour,
            }"
            :class="[
                useContrast(book.colour, 'text-zinc-900', 'text-white'),
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
                        class="absolute bottom-0 left-0 flex w-full items-end p-4 opacity-0 transition-all duration-300 h-11/12 group-hover:opacity-100"
                        :style="{backgroundImage: `linear-gradient(to top, ${changeColourOpacity(book.colour, 1)}, rgba(0, 0, 0, 0))`}">
                        <div class="flex w-full translate-y-4 flex-col opacity-0 gap-1 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                            <h2 class="font-serif font-semibold text-lg/5 line-clamp-5">
                                {{ book.title }}
                            </h2>
                            <p
                                v-if="book.authors"
                                class="text-xs">
                                {{ book.authors.map(a => a.name).join(', ') }}
                            </p>
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    </article>
</template>
