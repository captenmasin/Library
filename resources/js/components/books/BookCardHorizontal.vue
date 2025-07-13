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
        class="flex w-full flex-col gap-2 md:items-center"
        :class="narrow ? '' : 'md:flex-row md:gap-8'">
        <div class="flex w-full gap-4">
            <component
                :is="linkTag"
                :href="url"
                :target="target"
                prefetch>
                <div class="w-20 shrink-0 overflow-hidden rounded-sm shadow-sm aspect-book md:w-22">
                    <img
                        :src="book.cover ?? DefaultCover"
                        :alt="`Book cover image for ${book.title}`"
                        class="bg-gray-200 object-cover size-full">
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
                            :class="isLink ? 'hover:text-primary' : ''"
                            class="font-serif transition-colors line-clamp-1 text-base/5 md:line-clamp-2 md:text-lg/6">
                            {{ book.title }}
                        </h3>
                    </component>
                </div>
                <p class="text-xs mt-0.5 line-clamp-1 text-muted-foreground/65 md:text-sm">
                    By {{ book.authors?.map((a) => a.name).join(', ') }}
                </p>
                <p
                    v-if="book.description"
                    class="mt-1 text-xs line-clamp-2 text-muted-foreground">
                    {{ book.description_clean }}
                </p>
            </div>
        </div>
        <div
            v-if="includeActions"
            class="-mt-11 w-full shrink-0 pl-24 max-w-64 md:ml-auto md:w-40 md:max-w-none md:pl-0">
            <BookActions :book="book" />
        </div>
    </div>
</template>
