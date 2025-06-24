<script setup>
import Image from '@/components/Image.vue'
import { ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { router, useForm } from '@inertiajs/vue3'
import { Label } from '@/components/ui/label/index.js'
import { Button } from '@/components/ui/button/index.js'

const props = defineProps({
    book: Object
})

const form = useForm({
    _method: 'PUT',
    cover: null
})

const coverPreview = ref(null)
const coverInput = ref(null)

const updateBookInformation = () => {
    if (coverInput.value) {
        form.cover = coverInput.value.files[0]
    }

    form.post(useRoute('books.cover.update', { book: props.book }), {
        errorBag: 'updateBookInformation',
        preserveScroll: true,
        onSuccess: () => clearCoverFileInput()
    })
}

const selectNewCover = () => {
    coverInput.value.click()
}

const updateCoverPreview = () => {
    const cover = coverInput.value.files[0]

    if (!cover) return

    const reader = new FileReader()

    reader.onload = (e) => {
        coverPreview.value = e.target.result
    }

    reader.readAsDataURL(cover)
}

const deleteCover = () => {
    router.delete(useRoute('books.cover.destroy', { book: props.book }), {
        preserveScroll: true,
        onSuccess: () => {
            coverPreview.value = null
            clearCoverFileInput()
        }
    })
}

const clearCoverFileInput = () => {
    if (coverInput.value?.value) {
        coverInput.value.value = null
    }
}
</script>

<template>
    <div>
        <form @submit.prevent="updateBookInformation">
            <div
                class="col-span-6 sm:col-span-4">
                <input
                    id="avatar"
                    ref="coverInput"
                    type="file"
                    class="hidden"
                    accept="image/*"
                    @change="updateCoverPreview"
                >

                <Label
                    for="cover"
                    value="Cover" />

                <div
                    v-show="! coverPreview"
                    class="mt-2">
                    <Image
                        :src="book.cover"
                        width="80"
                        class="rounded-md w-20 aspect-cover" />
                </div>

                <div
                    v-show="coverPreview"
                    class="mt-2">
                    <span
                        class="block rounded-full bg-cover bg-center bg-no-repeat size-20"
                        :style="'background-image: url(\'' + coverPreview + '\');'"
                    />
                </div>

                <Button
                    variant="secondary"
                    class="mt-2 me-2"
                    type="button"
                    @click.prevent="selectNewCover">
                    Select A New Cover
                </Button>

                <Button
                    v-if="book.has_custom_cover"
                    variant="secondary"
                    type="button"
                    class="mt-2"
                    @click.prevent="deleteCover"
                >
                    Remove Cover
                </Button>

                <div
                    class="mt-2"
                    v-html="form.errors.cover" />
            </div>

            <div>
                <div
                    v-if="form.recentlySuccessful"
                    class="me-3">
                    Saved.
                </div>

                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing">
                    Save
                </Button>
            </div>
        </form>
    </div>
</template>
