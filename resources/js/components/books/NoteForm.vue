<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import PlaceholderPattern from '@/components/PlaceholderPattern.vue'
import { Book } from '@/types/book'
import { nextTick, PropType, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Link, useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { Textarea } from '@/components/ui/textarea'
import { useMarkdown } from '@/composables/useMarkdown'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    }
})

const noteForm = useForm({
    content: props.book.notes?.content || ''
})

const displayNoteForm = ref(false)

const submit = () => {
    noteForm.post(useRoute('notes.store', props.book), {
        preserveScroll: true,
        onSuccess: () => {
            displayNoteForm.value = false
            noteForm.content = ''
        }
    })
}

function openNoteForm () {
    displayNoteForm.value = true
}
</script>

<template>
    <div class="mt-1">
        <div v-if="book.notes && !displayNoteForm">
            <div
                class="text-sm"
                v-html="book.notes.content_html" />

            <div class="mt-4 flex items-center gap-4">
                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <Link
                                :href="
                                    useRoute('notes.destroy', {
                                        note: book.notes,
                                    })
                                "
                                :on-success="
                                    () => {
                                        noteForm.content = '';
                                    }
                                "
                                class="cursor-pointer text-destructive/75 hover:text-destructive"
                                preserve-scroll
                                method="delete"
                            >
                                <Icon
                                    name="Trash"
                                    class="w-4" />
                            </Link>
                        </TooltipTrigger>
                        <TooltipContent> Delete note </TooltipContent>
                    </Tooltip>
                </TooltipProvider>

                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <button
                                class="cursor-pointer text-gray-600 hover:text-gray-900"
                                @click="displayNoteForm = true">
                                <Icon
                                    name="Pencil"
                                    class="w-4" />
                            </button>
                        </TooltipTrigger>
                        <TooltipContent> Edit note </TooltipContent>
                    </Tooltip>
                </TooltipProvider>
            </div>
        </div>
        <div v-if="!book.notes && !displayNoteForm">
            <div class="relative z-10 flex">
                <Button
                    variant="link"
                    @click="openNoteForm">
                    <Icon
                        name="Pencil"
                        class="w-4" />
                    Add a note
                </Button>
            </div>
        </div>
        <form
            v-if="displayNoteForm"
            @submit.prevent="submit">
            <Textarea
                v-model="noteForm.content"
                autofocus
                class="min-h-28"
                placeholder="Add notes about this book..." />
            <InputError :message="noteForm.errors.content" />
            <div class="flex items-center justify-end">
                <Button
                    variant="link"
                    @click="displayNoteForm = false; noteForm.clearErrors()">
                    Cancel
                </Button>
                <Button variant="link">
                    Save
                </Button>
            </div>
        </form>
    </div>
</template>
