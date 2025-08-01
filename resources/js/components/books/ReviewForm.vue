<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import UserAvatar from '@/components/UserAvatar.vue'
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
import { useAuthedUser } from '@/composables/useAuthedUser'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    existingReview: Object as PropType<Review | null>
})

const hasExistingReview = computed(() => !!props.existingReview)

const { authed, authedUser } = useAuthedUser()

const form = useForm({
    title: props.existingReview?.title || '',
    content: props.existingReview?.content || ''
})

const displayForm = ref(false)

function submit () {
    console.log(useRoute('reviews.store', props.book))
    form.post(useRoute('reviews.store', props.book), {
        preserveScroll: true,
        only: ['reviews', 'book'],
        onSuccess: () => {
            displayForm.value = false
            form.defaults()
        },
        onError: () => {
            alert('NO')
        }
    })
}

function deleteReview () {
    if (props.existingReview) {
        displayForm.value = false
        form.title = ''
        form.content = ''

        form.defaults()
    }
}
</script>

<template>
    <div>
        <div
            v-if="authed && authedUser && !displayForm && !hasExistingReview"
            class="py-8 items-center flex text-center gap-2 border-2 border-dashed border-primary/20 rounded flex-col justify-center">
            <UserAvatar
                :user="authedUser"
                class="size-10 md:size-14"
                :size="64"
                font-size="text-lg md:text-xl" />

            <h2 class="font-semibold text-lg md:text-2xl font-serif">
                Share your thoughts
            </h2>

            <Button
                class="md:mt-3"
                @click="displayForm = true">
                <Icon
                    name="Pencil" />
                Write a review
            </Button>
        </div>

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
                    id="dewlnnlwe"
                    type="submit"
                    :disabled="form.processing">
                    {{ hasExistingReview ? 'Update Review' : 'Submit Review' }}
                </Button>
            </div>
        </form>

        <SingleReview
            v-if="!displayForm && hasExistingReview && existingReview"
            :review="existingReview"
            :book="book"
            class="mb-4 border-2 border-dashed border-secondary rounded p-4"
            @deleted="deleteReview" />

        <div
            v-if="!displayForm"
            class="mb-4 flex w-full gap-4 justify-end items-end">
            <Button
                v-if="hasExistingReview"
                variant="secondary"
                @click="displayForm = true">
                <Icon
                    name="pencil"
                    class="mr-2" />
                Edit review
            </Button>
        </div>
    </div>
</template>
