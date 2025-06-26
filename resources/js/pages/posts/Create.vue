<script setup lang="ts">
import Image from '@/components/Image.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { PropType } from 'vue'
import { Book } from '@/types/book'
import { useForm } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { useRoute } from '@/composables/useRoute'
import { Textarea } from '@/components/ui/textarea'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'

const props = defineProps({
    books: {
        type: Array as PropType<Book[]>
    }
})

const form = useForm({
    title: '',
    content: '',
    book_identifier: null,
    featured_image: null
})

function submit () {
    form.post(useRoute('posts.store'), {
        onSuccess: () => {
            form.reset()
        }
    })
}
</script>

<template>
    <AppLayout>
        <h1>Create Post</h1>
        <form @submit.prevent="submit">
            <div>
                <label for="title">Title</label>
                <Input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required />
            </div>
            <div>
                <label for="content">Content</label>
                <Textarea
                    id="content"
                    v-model="form.content"
                    required />
            </div>
            <div>
                <label for="image">Image</label>
                <Input
                    id="image"
                    type="file"
                    @change="e => form.featured_image = e.target.files[0]" />
            </div>
            <div v-if="books && books.length > 0">
                <label for="book">Book</label>
                <Select v-model="form.book_identifier">
                    <SelectTrigger class="w-full">
                        <SelectValue placeholder="Select book" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem
                                v-for="book in books"
                                :key="book.identifier"
                                :value="book.identifier">
                                <Image
                                    :src="book.cover"
                                    width="50"
                                    :scale="true" />
                                {{ book.title }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <Button type="submit">
                    Create Post
                </Button>
            </div>
        </form>

        <div v-if="form.errors">
            <ul>
                <li
                    v-for="(error, key) in form.errors"
                    :key="key">
                    {{ error }}
                </li>
            </ul>
        </div>
    </AppLayout>
</template>
