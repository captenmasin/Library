<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import DefaultCover from '~/images/default-cover.svg'
import { Link } from '@inertiajs/vue3'
import { computed, PropType } from 'vue'
import { BookApiResult } from '@/types/book'
import { Button } from '@/components/ui/button'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    book: {
        type: Object as PropType<BookApiResult>,
        required: true
    },
    narrow: {
        type: Boolean,
        default: false
    },
    target: {
        type: String as PropType<'_blank' | '_self'>,
        default: '_self'
    }
})

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } = useUserBookStatus()

function select (book: BookApiResult, status: UserBookStatus) {
    if (book?.identifier) {
        if (addedBookIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        } else {
            addBookToUser(book.identifier, status)
        }
    }
}

const linkTag = computed(() => {
    if (props.target === '_blank') {
        return 'a'
    }

    if (props.book.links?.show) {
        return Link
    }

    return 'span'
})
</script>

<template>
    <div
        class="flex items-center"
        :class="narrow ? 'flex-col gap-1' : 'gap-4'">
        <div class="flex items-center gap-4">
            <component
                :is="linkTag"
                :href="book.links?.show ?? null"
                :target="target"
                prefetch>
                <div class="shrink-0 overflow-hidden rounded-sm shadow-sm aspect-book w-22">
                    <img
                        :src="book.cover ?? DefaultCover"
                        :alt="`Book cover image for ${book.title}`"
                        class="bg-gray-200 object-cover size-full">
                </div>
            </component>
            <div class="flex flex-col">
                <div class="flex">
                    <component
                        :is="linkTag"
                        :href="book.links?.show ?? null"
                        :target="target"
                        prefetch>
                        <h3 class="font-serif text-lg/6">
                            {{ book.title }}
                        </h3>
                    </component>
                </div>
                <p class="text-sm mt-0.5 text-muted-foreground">
                    By {{ book.authors?.map((a) => a.name).join(', ') }}
                </p>
                <p
                    v-if="book.description"
                    class="mt-1 text-xs text-muted-foreground line-clamp-2">
                    {{ book.description_clean }}
                </p>
            </div>
        </div>
        <div class="ml-auto flex shrink-0 items-center justify-end gap-2 w-78">
            <div
                v-if="addingBooks.includes(book.identifier)"
                class="animate-spin rounded-full border border-gray-200 bg-gray-100 p-1 text-gray-600">
                <Icon
                    name="LoaderCircle"
                    class="w-4"
                />
            </div>

            <Button
                v-if="addedBookIdentifiers.has(book.identifier)"
                @click="removeBookFromUser(book)">
                Remove
            </Button>

            <div class="w-44">
                <Select
                    v-model="selectedStatuses[book.identifier]"
                    @update:model-value="value => select(book, value as UserBookStatus)">
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
