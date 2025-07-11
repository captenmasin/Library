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
    content: ''
})

const displayNoteForm = ref(false)

const submit = () => {
    noteForm.post(useRoute('notes.store', props.book), {
        preserveScroll: true,
        onSuccess: () => {
            displayNoteForm.value = false
        }
    })
}

const noteInput = ref<HTMLInputElement | null>(null)

function openNoteForm () {
    displayNoteForm.value = true
    nextTick(() => {
        if (noteInput.value) {
            noteInput.value.focus()
        }
    })
}
</script>

<template>
    <div>
        <div v-if="!displayNoteForm">
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
                ref="noteInput"
                v-model="noteForm.content"
                class="min-h-28"
                placeholder="Your note..." />
            <InputError :message="noteForm.errors.content" />
            <div class="flex items-center justify-end">
                <Button
                    variant="link"
                    type="button"
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
