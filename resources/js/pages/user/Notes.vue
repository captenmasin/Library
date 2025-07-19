<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import PageTitle from '@/components/PageTitle.vue'
import BookCardHorizontal from '@/components/books/BookCardHorizontal.vue'
import SingleNote from '@/components/SingleNote.vue'
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

const loadingMore = ref(false)

function loadMore () {
    router.reload({
        data: { page: props.notes.meta.current_page + 1 },
        only: ['notes'],
        preserveState: true,
        preserveScroll: true,
        onBefore: () => { loadingMore.value = true },
        onFinish: () => { loadingMore.value = false }
    })
}
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
        <div
            v-if="props.notes.links.next"
            class="mt-4 mb-36 flex items-center justify-center"
        >
            <Button
                variant="secondary"
                :disabled="loadingMore"
                @click="loadMore"
            >
                <Icon
                    v-if="!loadingMore"
                    name="Plus"
                    class="w-4" />
                <Icon
                    v-else
                    name="LoaderCircle"
                    class="w-4 animate-spin" />
                Load more
            </Button>
        </div>
    </div>
</template>
