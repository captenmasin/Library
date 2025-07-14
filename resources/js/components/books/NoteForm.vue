<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import PlaceholderPattern from '@/components/PlaceholderPattern.vue'
import { Book } from '@/types/book'
import { nextTick, PropType, ref } from 'vue'
import { Label } from '@/components/ui/label'
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

const displayNoteForm = ref(true)

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
        <form
            v-if="displayNoteForm"
            @submit.prevent="submit">
            <Textarea
                id="noteInput"
                ref="noteInput"
                v-model="noteForm.content"
                class="min-h-18"
                placeholder="Add a private note about this book..." />
            <InputError :message="noteForm.errors.content" />
            <div class="flex mt-2 justify-between">
                <div>
                    <a
                        href="https://www.markdownguide.org/"
                        class="text-secondary-foreground text-sm hover:underline"
                        target="_blank"
                        rel="nofollow ">
                        Markdown syntax is supported.
                    </a>
                </div>
                <div class="flex items-center gap-4 ">
                    <Button
                        v-if="noteForm.isDirty"
                        variant="secondary"
                        type="button"
                        @click="noteForm.reset()">
                        Cancel
                    </Button>
                    <Button
                        variant="default"
                        type="submit">
                        Save
                    </Button>
                </div>
            </div>
        </form>
    </div>
</template>
