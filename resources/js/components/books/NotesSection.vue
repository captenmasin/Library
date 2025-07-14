<script setup lang="ts">
import SingleNote from '@/components/SingleNote.vue'
import NoteForm from '@/components/books/NoteForm.vue'
import { Book } from '@/types/book'
import { computed, PropType, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    }
})

const showAllNotes = ref(false)
const displayLimit = ref(3)

const displayNotes = computed(() => {
    if (!props.book.user_notes) {
        return []
    }
    if (showAllNotes.value) {
        return props.book.user_notes
    }
    return props.book.user_notes.slice(0, displayLimit.value)
})
</script>

<template>
    <div class="flex flex-col">
        <NoteForm :book="book" />
        <div class="mt-4">
            <div class="flex flex-col divide-y divide-muted">
                <SingleNote
                    v-for="note in displayNotes"
                    :key="note.id"
                    :book="book"
                    :note="note" />
            </div>

            <div
                v-if="book.user_notes && book.user_notes?.length > displayLimit"
                class="my-3 flex items-center md:my-6">
                <Separator class="flex flex-1 bg-muted" />
                <div class="flex px-4">
                    <Button
                        variant="link"
                        size="sm"
                        class="text-secondary-foreground"
                        @click="showAllNotes = !showAllNotes">
                        {{ showAllNotes ? 'Show less' : 'Show all' }}
                    </Button>
                </div>
                <Separator class="flex flex-1 bg-muted" />
            </div>
        </div>
    </div>
</template>
