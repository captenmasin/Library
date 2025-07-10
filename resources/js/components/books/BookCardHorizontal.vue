<script setup lang="ts">
import DefaultCover from '~/images/default-cover.svg'
import BookActions from '@/components/books/BookActions.vue'
import { Link } from '@inertiajs/vue3'
import { computed, PropType } from 'vue'
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
</script>

<template>
    <div
        class="flex flex-col md:items-center gap-2 w-full"
        :class="narrow ? '' : 'md:flex-row md:gap-8'">
        <div class="flex gap-4 w-full">
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
            <div class="flex flex-col min-w-0 w-full">
                <div class="flex">
                    <component
                        :is="linkTag"
                        :href="url"
                        :target="target"
                        prefetch>
                        <h3
                            :class="isLink ? 'hover:text-primary' : ''"
                            class="line-clamp-1 md:line-clamp-2 font-serif transition-colors text-base/5 md:text-lg/6">
                            {{ book.title }}
                        </h3>
                    </component>
                </div>
                <p class="mt-0.5 line-clamp-1 text-xs text-muted-foreground/65 md:text-sm">
                    By {{ book.authors?.map((a) => a.name).join(', ') }}
                </p>
                <p
                    v-if="book.description"
                    class="mt-1 line-clamp-2 text-xs text-muted-foreground">
                    {{ book.description_clean }}
                </p>
            </div>
        </div>
        <div
            v-if="includeActions"
            class="md:ml-auto max-w-64 md:max-w-none -mt-11 pl-24 md:pl-0 shrink-0 w-full md:w-40">
            <BookActions :book="book" />
        </div>
    </div>
</template>
