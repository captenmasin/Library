<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import { Book } from '@/types/book'
import { computed, PropType, ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { router, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button/index.js'

const props = defineProps({
    book: Object as PropType<Book>
})

const key = ref(0)

const form = useForm({
    cover: null
})

const canRemoveCover = ref(props.book && props.book.has_custom_cover)

const canUpdateCover = computed(() => {
    return props.book && props.book.in_library
})

const displayUndo = ref(false)
const coverPreview = ref(null)
const coverInput = ref(null)

const clickCoverInput = () => {
    if (coverInput.value) {
        coverInput.value.click()
    }
}

const updateBookInformation = () => {
    if (coverInput.value) {
        form.cover = coverInput.value.files[0]
    }

    form.post(useRoute('cover.update', { book: props.book }), {
        errorBag: 'bookCoverBag',
        preserveScroll: true,
        onSuccess: () => {
            clearCoverFileInput()
            canRemoveCover.value = true
            key.value++

            setTimeout(() => {
                coverPreview.value = null
            }, 500)
        }
    })
}

const updateCoverPreview = () => {
    const cover = coverInput.value.files[0]
    displayUndo.value = true
    canRemoveCover.value = false

    if (!cover) return

    const reader = new FileReader()

    reader.onload = (e) => {
        coverPreview.value = e.target.result
    }

    reader.readAsDataURL(cover)
}

const deleteCover = () => {
    router.delete(useRoute('cover.destroy', { book: props.book }), {
        preserveScroll: true,
        onSuccess: () => {
            coverPreview.value = null
            canRemoveCover.value = false
            clearCoverFileInput()
            key.value++
        }
    })
}

const clearCoverFileInput = () => {
    if (coverInput.value?.value) {
        coverInput.value.value = null
    }

    displayUndo.value = false
}

const reset = () => {
    coverPreview.value = null
    clearCoverFileInput()
    displayUndo.value = false

    canRemoveCover.value = props.book && props.book.has_custom_cover
    key.value++
}
</script>

<template>
    <div>
        <div class="relative">
            <div
                v-if="canUpdateCover"
                :key="key"
                :class="coverPreview ? 'opacity-100' : 'opacity-0'"
                class="absolute top-0 bottom-0 left-0 flex w-full gap-2 transition-all hover:opacity-100">
                <div class="absolute bottom-2 flex w-full items-center gap-2 p-2">
                    <Button
                        v-if="!coverPreview"
                        size="sm"
                        class="flex-1 cursor-pointer rounded-full text-xs"
                        @click="clickCoverInput"
                    >
                        <Icon
                            name="ImagePlus"
                            class="w-3" />
                        Update
                    </Button>

                    <Button
                        v-if="coverPreview"
                        variant="ghost"
                        size="sm"
                        class="flex-1 cursor-pointer rounded-full bg-green-200/75 text-xs text-green-700 backdrop-blur-lg hover:bg-green-200 hover:text-green-800"
                        @click="updateBookInformation"
                    >
                        <Icon
                            name="Save"
                            class="w-3" />
                        Save
                    </Button>

                    <Button
                        v-if="coverPreview && displayUndo"
                        variant="ghost"
                        size="sm"
                        class="flex-1 cursor-pointer rounded-full bg-white/75 text-xs backdrop-blur-lg"
                        @click="reset"
                    >
                        <Icon
                            name="Undo2"
                            class="w-3" />
                        Undo
                    </Button>

                    <Button
                        v-if="canRemoveCover"
                        type="button"
                        size="icon"
                        variant="ghost"
                        class="cursor-pointer rounded-full text-xs backdrop-blur-lg bg-destructive/75 text-destructive-foreground"
                        @click.prevent="deleteCover"
                    >
                        <Icon
                            name="X"
                            class="w-3" />
                    </Button>
                </div>

                <input
                    v-if="canUpdateCover"
                    id="coverInput"
                    ref="coverInput"
                    type="file"
                    class="hidden"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                    @change="updateCoverPreview"
                >
            </div>

            <div
                v-show="!coverPreview || !canUpdateCover">
                <slot />
            </div>

            <form
                v-if="canUpdateCover"
                @submit.prevent="updateBookInformation">
                <div class="col-span-6 sm:col-span-4">
                    <div
                        v-show="coverPreview"
                        class="aspect-book">
                        <img
                            :src="coverPreview"
                            alt="Cover Preview"
                            class="rounded-md object-cover size-full">
                    </div>
                </div>
            </form>
        </div>
        <InputError
            v-if="canUpdateCover && form.errors.cover"
            class="mt-2"
            :message="form.errors.cover" />
    </div>
</template>
