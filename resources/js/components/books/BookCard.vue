<script setup lang="ts">
import Image from '@/components/Image.vue'
import StarRatingDisplay from '@/components/StarRatingDisplay.vue'
import type { PropType } from 'vue'
import { Link } from '@inertiajs/vue3'
import type { Book } from '@/types/book'
import { useBook } from '@/composables/useBook'
import { useColours } from '@/composables/useColours'
import { useContrast } from '@/composables/useContrast'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    hover: {
        type: Boolean,
        default: true
    }
})

const { changeColourOpacity } = useColours()
const { userRating } = useBook(props.book)
</script>

<template>
    <article class="flex gap-4">
        <div
            class="flex w-full flex-col overflow-hidden rounded-md shadow-sm group"
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
                    {{ book.binding }}
                    <Image
                        v-if="book.cover"
                        :src="book.cover"
                        :height="315"
                        :width="200"
                        class="h-full w-full object-cover" />
                    <div
                        v-if="hover"
                        class="absolute bottom-0 left-0 flex w-full items-end p-4 opacity-0 transition-all duration-300 h-full group-hover:opacity-100"
                        :style="{backgroundImage: `linear-gradient(to top, ${changeColourOpacity(book.colour, 1)}, rgba(0, 0, 0, 0))`}">
                        <div class="flex w-full translate-y-4 flex-col gap-1.5 opacity-0 transition-all duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                            <h2 class="font-serif font-semibold text-base line-clamp-4 leading-5">
                                {{ book.title }}
                            </h2>
                            <p
                                v-if="book.authors"
                                class="text-xs line-clamp-2">
                                {{ book.authors.map(a => a.name).join(', ') }}
                            </p>
                            <StarRatingDisplay
                                v-if="userRating"
                                class="mt-1"
                                text-class="text-inherit"
                                :rating="userRating.value"
                                :star-width="15" />
                        </div>
                    </div>
                </div>
            </Link>
        </div>
    </article>
</template>
