<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { PropType } from 'vue'
import { Button } from '@/components/ui/button'
import { Book, BookApiResult } from '@/types/book'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { useUserBookStatus } from '@/composables/useUserBookStatus'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

defineProps({
    book: {
        type: Object as PropType<Book | BookApiResult>,
        required: true
    }
})

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } =
    useUserBookStatus()

function selectNewStatus (book: BookApiResult | Book, status: UserBookStatus) {
    if (book?.identifier) {
        if (addedBookIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        } else {
            addBookToUser(book.identifier, status)
        }
    }
}
</script>

<template>
    <div class="flex w-full items-center justify-end gap-2 flex-1">
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

        <div class="w-full md:w-full">
            <Select
                v-model="selectedStatuses[book.identifier]"
                @update:model-value="(value) => selectNewStatus(book, value as UserBookStatus)">
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
</template>
