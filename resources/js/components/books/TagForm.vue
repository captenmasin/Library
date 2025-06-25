<script setup lang="ts">
import InputError from '@/components/InputError.vue'
import { PropType } from 'vue'
import { Book } from '@/types/book'
import { useForm } from '@inertiajs/vue3'
import { ErrorMessage } from 'vee-validate'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from '@/components/ui/tags-input'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    }
})

const tagForm = useForm({
    tags: props.book.user_tags || []
})

const submit = () => {
    tagForm.put(useRoute('user.books.update_tags', props.book), {
        preserveScroll: true
    })
}

</script>

<template>
    <div>
        <form @submit.prevent="submit">
            <TagsInput v-model="tagForm.tags">
                <TagsInputItem
                    v-for="item in tagForm.tags"
                    :key="item"
                    :value="item">
                    <TagsInputItemText />
                    <TagsInputItemDelete />
                </TagsInputItem>

                <TagsInputInput placeholder="Tags..." />
            </TagsInput>
            <Button> Save </Button>
            <InputError
                :message="tagForm.errors.tags"
                class="mt-2" />
        </form>
    </div>
</template>
