<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import SingleNote from '@/components/SingleNote.vue'
import { PropType } from 'vue'
import { Note } from '@/types/note'

defineOptions({ layout: AppLayout })

const props = defineProps({
    notes: {
        type: Array as PropType<Note[]>,
        default: () => []
    }
})
</script>

<template>
    <div>
        <PageTitle class="mb-4">Your Notes</PageTitle>

        <ul class="divide-y divide-muted rounded-xl bg-white shadow">
            <li
                v-for="note in props.notes"
                :key="note.id"
                class="p-4 flex flex-col gap-4 md:flex-row"
            >
                <BookCardHorizontal
                    :book="note.book"
                    :include-actions="false"
                    class="md:w-1/3"
                />
                <SingleNote
                    :book="note.book"
                    :note="note"
                    class="flex-1"
                />
            </li>
        </ul>
    </div>
</template>
