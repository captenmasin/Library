<script setup lang="ts">
import { PropType } from 'vue'
import { Book } from '@/types/book'
import { Note } from '@/types/note'
import { Link } from '@inertiajs/vue3'
import { useDateFormat } from '@vueuse/core'
import { Badge } from '@/components/ui/badge'
import { useRoute } from '@/composables/useRoute'
import { useMarkdown } from '@/composables/useMarkdown'

defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    note: {
        type: Object as PropType<Note>,
        required: true
    }
})

function formatDate (date) {
    return useDateFormat(date, 'Mo MMMM h:ma')
}
</script>

<template>
    <div class="group py-6">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-secondary-foreground">
                {{ formatDate(note.created_at) }}
            </div>
            <div class="flex items-center gap-2">
                <div class="flex transition-all group-hover:opacity-100 md:opacity-0">
                    <Link
                        :href="useRoute('notes.destroy', { book: book, note: note.id })"
                        preserve-scroll
                        class="cursor-pointer text-xs text-destructive hover:underline"
                        method="delete"
                    >
                        Delete
                    </Link>
                </div>
                <Badge
                    variant="secondary"
                    class="text-xs">
                    {{ note.status }}
                </Badge>
            </div>
        </div>
        <div
            class="prose prose-sm mt-2 max-w-none"
            v-html="useMarkdown(note.content)" />
    </div>
</template>
