<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Book } from '@/types/book'
import { Review } from '@/types/review'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { computed, PropType, ref } from 'vue'
import { Button } from '@/components/ui/button'
import { Link, useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { Textarea } from '@/components/ui/textarea'
import { useMarkdown } from '@/composables/useMarkdown'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

const props = defineProps({
    book: Object as PropType<Book>,
    existingReview: Object as PropType<Review | null>
})

const hasExistingReview = computed(() => !!props.existingReview)

const form = useForm({
    rating: props.existingReview?.rating || 0,
    title: props.existingReview?.title || '',
    content: props.existingReview?.content || ''
})

function updateRating (rating: number) {
    form.rating = rating
    submit()
}

const displayForm = ref(false)

function submit () {
    form.post(useRoute('reviews.store', props.book), {
        preserveScroll: true
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

        <div
            v-if="!displayForm && hasExistingReview && existingReview"
            class="mb-4">
            <h3 class="text-lg font-semibold">
                Your Review
            </h3>
            <div>
                <p class="font-serif text-lg font-semibold">
                    {{ existingReview.title }}
                </p>
                <ul
                    v-if="existingReview.rating > 0"
                    class="flex items-center space-x-0.5">
                    <li
                        v-for="star in 5"
                        :key="star">
                        <Icon
                            name="Star"
                            :class="star <= existingReview.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'"
                            class="size-4" />
                    </li>
                </ul>
                <div
                    class="mt-1 prose"
                    v-html="useMarkdown(existingReview.content)" />

                <div class="mt-4 flex items-center gap-4">
                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <!--                                <Link-->
                                <!--                                    :href="-->
                                <!--                                        useRoute('notes.destroy', {-->
                                <!--                                            note: book.notes,-->
                                <!--                                        })-->
                                <!--                                    "-->
                                <!--                                    :on-success="-->
                                <!--                                        () => {-->
                                <!--                                            noteForm.content = '';-->
                                <!--                                        }-->
                                <!--                                    "-->
                                <!--                                    class="cursor-pointer text-destructive/75 hover:text-destructive"-->
                                <!--                                    preserve-scroll-->
                                <!--                                    method="delete"-->
                                <!--                                >-->
                                <!--                                    <Icon-->
                                <!--                                        name="Trash"-->
                                <!--                                        class="w-4" />-->
                                <!--                                </Link>-->
                                deleteeeeee
                            </TooltipTrigger>
                            <TooltipContent> Delete note </TooltipContent>
                        </Tooltip>
                    </TooltipProvider>

                    <TooltipProvider>
                        <Tooltip>
                            <TooltipTrigger as-child>
                                <button
                                    class="cursor-pointer text-gray-600 hover:text-gray-900"
                                    @click="displayForm = true">
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
        </div>

        <div v-if="!displayForm && !hasExistingReview">
            <Button @click="displayForm = true">
                Add a Review
            </Button>
        </div>
    </div>
</template>
