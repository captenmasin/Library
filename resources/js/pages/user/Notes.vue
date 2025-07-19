<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import SingleNote from '@/components/SingleNote.vue'
import Pagination from '@/components/Pagination.vue'
import Icon from '@/components/Icon.vue'
import { PropType, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button/index.js'
import { Note } from '@/types/note'
import { Paginated } from '@/types/pagination'

defineOptions({ layout: AppLayout })

const props = defineProps({
    notes: {
        type: Object as PropType<Paginated<Note>>,
        default: () => ({ data: [], links: {}, meta: {} })
    }
})
</script>

<template>
    <div>
        <PageTitle class="mb-4">Your Notes</PageTitle>

        <ul class="divide-y divide-muted rounded-xl bg-white shadow">
            <li
                v-for="note in props.notes.data"
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
        <Pagination :meta="props.notes.meta" />
    </div>
</template>
