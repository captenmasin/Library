<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import DefaultCover from '~/images/default-cover.svg'
import { Link } from '@inertiajs/vue3'
import { computed, PropType } from 'vue'
import { Button } from '@/components/ui/button'
import { Book, BookApiResult } from '@/types/book'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

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

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } =
    useUserBookStatus()

function select (book: BookApiResult | Book, status: UserBookStatus) {
    if (book?.identifier) {
        if (addedBookIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        } else {
            addBookToUser(book.identifier, status)
        }
    }
}

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
        class="flex flex-col items-center gap-2 w-full"
        :class="narrow ? '' : 'md:flex-row md:gap-8'">
        <div class="flex gap-4 w-full">
            <component
                :is="linkTag"
                :href="url"
                :target="target"
                prefetch>
                <div class="aspect-book w-18 shrink-0 overflow-hidden rounded-sm shadow-sm md:w-22">
                    <img
                        :src="book.cover ?? DefaultCover"
                        :alt="`Book cover image for ${book.title}`"
                        class="size-full bg-gray-200 object-cover">
                </div>
            </component>
            <div class="flex flex-col">
                <div class="flex">
                    <component
                        :is="linkTag"
                        :href="url"
                        :target="target"
                        prefetch>
                        <h3
                            :class="isLink ? 'hover:text-primary' : ''"
                            class="line-clamp-2 font-serif transition-colors text-base/5 md:line-clamp-4 md:text-lg/6">
                            {{ book.title }}
                        </h3>
                    </component>
                </div>
                <p class="mt-0.5 text-xs text-muted-foreground md:text-sm">
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
            class="ml-auto flex w-full shrink-0 items-center justify-end gap-2 md:w-full flex-1">
            <div
                v-if="addingBooks.includes(book.identifier)"
                class="w-9 shrink-0 aspect-square">
                <div
                    class="animate-spin rounded-full bg-muted size-full flex items-center justify-center text-muted-foreground">
                    <Icon
                        name="LoaderCircle"
                        class="size-5" />
                </div>
            </div>

            <TooltipProvider v-if="addedBookIdentifiers.has(book.identifier)">
                <Tooltip>
                    <TooltipTrigger>
                        <Button
                            variant="secondary"
                            size="icon"
                            @click="removeBookFromUser(book)"
                        >
                            <Icon
                                name="Trash"
                                class="w-4" />
                        </Button>
                    </TooltipTrigger>
                    <TooltipContent> Remove from library </TooltipContent>
                </Tooltip>
            </TooltipProvider>

            <div class="w-full md:w-44">
                <Select
                    v-model="selectedStatuses[book.identifier]"
                    @update:model-value="(value) => select(book, value as UserBookStatus)">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Add to library" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem
                                v-for="status in possibleStatuses"
                                :key="status.value"
                                :value="status.value">
                                {{ status.label }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
        </div>
    </div>
</template>
