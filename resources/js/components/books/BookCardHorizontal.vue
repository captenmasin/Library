<script setup lang="ts">
import DefaultCover from '~/images/default-cover.svg'
import BookActions from '@/components/books/BookActions.vue'
import StarRatingDisplay from '@/components/StarRatingDisplay.vue'
import { Link } from '@inertiajs/vue3'
import { computed, PropType } from 'vue'
import { useBook } from '@/composables/useBook'
import { Book, BookApiResult } from '@/types/book'

const props = defineProps({
    book: {
        type: Object as PropType<BookApiResult | Book>,
        required: true
    },
    narrow: {
        type: Boolean,
        default: false
    },
    includeActions: {
        type: Boolean,
        default: true
    },
    target: {
        type: String as PropType<'_blank' | '_self'>,
        default: '_self'
    }
})

const isLink = computed(() => {
    return props.book.links?.show !== undefined && props.book.links?.show !== null
})

const linkTag = computed(() => {
    if (props.target === '_blank') {
        return 'a'
    }

    if (props.book.links?.show) {
        return Link
    }

    return 'span'
})

const url = computed(() => {
    return props.book.links?.show ?? null
})

const { userRating } = useBook(props.book)
</script>

<template>
    <div
        class="flex w-full flex-col gap-2 md:items-center"
        :class="narrow ? '' : 'md:flex-row md:gap-8'">
        <div class="flex w-full gap-4">
            <component
                :is="linkTag"
                :href="url"
                :target="target"
                prefetch>
                <div class="aspect-book w-20 shrink-0 overflow-hidden rounded-sm shadow-sm md:w-22">
                    <img
                        :src="book.cover ?? DefaultCover"
                        :alt="`Book cover image for ${book.title}`"
                        class="size-full bg-gray-200 object-cover">
                </div>
            </component>
            <div class="flex w-full min-w-0 flex-col">
                <div class="flex">
                    <component
                        :is="linkTag"
                        :href="url"
                        :target="target"
                        prefetch>
                        <h3
                            :class="isLink ? 'hover:text-primary dark:hover:text-primary/80' : ''"
                            class="line-clamp-1 font-serif text-lg transition-colors md:line-clamp-2 md:text-lg/6 text-pretty"
                        >
                            {{ book.title }}
                        </h3>
                    </component>
                </div>
                <p class="-mt-0.5 md:mt-0.5 line-clamp-1 text-xs text-muted-foreground/65 md:text-sm">
                    By {{ book.authors?.map((a) => a.name).join(', ') }}
                </p>
                <p
                    v-if="book.description"
                    class="hidden mt-0.5 md:mt-1 md:line-clamp-2 text-xs text-muted-foreground">
                    {{ book.description_clean }}
                </p>
                <StarRatingDisplay
                    v-if="userRating"
                    class="mt-1"
                    :rating="userRating.value"
                    :star-width="15" />
            </div>
        </div>
        <div
            v-if="includeActions"
            :class="userRating ? '-mt-11' : '-mt-11'"
            class="w-full md:max-w-64 shrink-0 pl-24 md:ml-auto md:w-40 md:max-w-none md:pl-0">
            <BookActions :book="book" />
        </div>
    </div>
</template>
