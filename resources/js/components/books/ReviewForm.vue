<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import SingleReview from '@/components/SingleReview.vue'
import { Book } from '@/types/book'
import { Review } from '@/types/review'
import { useForm } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { computed, PropType, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { Textarea } from '@/components/ui/textarea'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    existingReview: Object as PropType<Review | null>
})

const hasExistingReview = computed(() => !!props.existingReview)

const form = useForm({
    title: props.existingReview?.title || '',
    content: props.existingReview?.content || ''
})

const displayForm = ref(false)

function submit () {
    form.post(useRoute('reviews.store', props.book), {
        preserveScroll: true,
        onSuccess: () => {
            displayForm.value = false
            form.defaults()
        }
    })
}
</script>

<template>
    <div>
        <form
            v-if="displayForm"
            class="mb-4 flex flex-col gap-4"
            @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="reviewTitle">Title</Label>
                <Input
                    id="reviewTitle"
                    v-model="form.title"
                />
            </div>
            <div class="grid gap-2">
                <Label for="reviewContent">Content</Label>
                <Textarea
                    id="reviewContent"
                    v-model="form.content"
                    class="w-full rounded border p-2"
                    rows="4"
                    placeholder="Write your thoughts..."
                />
            </div>
            <div class="flex justify-end">
                <Button
                    class="mr-2"
                    variant="link"
                    @click="displayForm = false; form.reset()">
                    Cancel
                </Button>
                <Button
                    type="submit"
                    :disabled="form.processing"
                >
                    {{ hasExistingReview ? 'Update Review' : 'Submit Review' }}
                </Button>
            </div>
        </form>

        <SingleReview
            v-if="!displayForm && hasExistingReview && existingReview"
            :review="existingReview"
            :book="book"
            class="mb-4 border border-accent rounded p-4" />
    </div>
</template>
