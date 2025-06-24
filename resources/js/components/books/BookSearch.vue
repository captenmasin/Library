<script setup>
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import DefaultCover from '~/images/default-cover.svg'
import { watchDebounced } from '@vueuse/core'
import { computed, onMounted, ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { useForm, usePage } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input/index.js'
import { Button } from '@/components/ui/button/index.js'
import { useRequest } from '@/composables/useRequest.js'
import { useUserBookStatus } from '@/composables/useUserBookStatus.js'
import { DialogClose, DialogTitle } from '@/components/ui/dialog/index.js'
import { Dialog, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index.js'

const keyword = ref('')
const author = ref('')
const recent = ref([])
const filteredBooks = ref([])
const loading = ref(false)

const added = ref({ ...usePage().props.auth.user_books })
const adding = ref([])
const selectedStatuses = ref({})

const addedIdentifiers = computed(() =>
    new Set(Object.keys(usePage().props.auth.user_books || {}))
)

const form = useForm({
    identifier: '',
    status: 'PlanToRead'
})

async function addBookToUser (identifier, status) {
    adding.value.push(identifier)
    try {
        const response = await useRequest(useRoute('api.books.fetch_or_create'), 'POST', { identifier })

        if (response?.book?.identifier) {
            const book = response.book
            form.identifier = book.identifier
            form.status = status || 'PlanToRead'
            form.post(useRoute('users.books.store'), {
                onSuccess: () => {
                    added.value[book.identifier] = form.status
                    adding.value = adding.value.filter((id) => id !== identifier)
                    recent.value.push(book)
                }
            })
        } else {
            adding.value = adding.value.filter((id) => id !== identifier)
            form.setError('identifier', 'Failed to create or fetch book')
            console.error('Missing identifier in book data:', response)
        }
    } catch (error) {
        adding.value = adding.value.filter((id) => id !== identifier)
        form.setError('identifier', 'Failed to create or fetch book')
        console.error('Error creating/fetching book:', error)
    }
}

async function removeBookFromUser (book) {
    useRequest(useRoute('api.books.fetch_by_identifier', { identifier: book.identifier }), 'GET')
        .then((response) => {
            const fetchedBook = response.data
            if (fetchedBook) {
                form.identifier = fetchedBook.identifier

                form.delete(useRoute('users.books.destroy', fetchedBook), {
                    onSuccess: () => {
                        delete added.value[fetchedBook.identifier]
                        delete selectedStatuses.value[fetchedBook.identifier]
                        form.identifier = ''
                    }
                })
            } else {
                console.error('Fetched book data is missing:', response)
            }
        })
        .catch((error) => {
            console.error('Error fetching book by identifier:', error)
        })
}

async function searchBooks () {
    if (!keyword.value && !author.value) {
        filteredBooks.value = []
        return
    }

    loading.value = true
    useRequest(useRoute('api.books.search', { q: keyword.value, author: author.value }), 'GET').then((response) => {
        filteredBooks.value = response
    }).catch((error) => {
        console.error('Error searching for books:', error)
        loading.value = false
        filteredBooks.value = []
    }).finally(() => {
        loading.value = false
    })
}

const { possibleStatuses, updateStatus } = useUserBookStatus()

watchDebounced([keyword, author], searchBooks, { debounce: 500 })

const hasSearch = computed(() => {
    return keyword.value !== '' || author.value !== ''
})

function select (book, status) {
    if (book?.identifier) {
        if (addedIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        }
        addBookToUser(book.identifier, status)
    }
}

onMounted(() => {
    selectedStatuses.value = { ...added.value }
})
</script>

<template>
    <div>
        <Dialog>
            <DialogTrigger as-child>
                <Button class="w-full">
                    <Icon
                        name="Plus"
                        class="w-4" />
                    Add Book
                </Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-4xl">
                <DialogTitle>Add a new book to your library</DialogTitle>

                <div class="flex gap-4">
                    <div class="flex flex-col w-1/3">
                        <Input
                            id="keyword-search"
                            v-model="keyword"
                            class="w-full"
                            placeholder="Search for books by title, author, or publisher" />
                        <Input
                            id="author-search"
                            v-model="author"
                            class="w-full"
                            placeholder="Author..." />
                    </div>
                    <div class="flex flex-col w-2/3">
                        <Loader
                            v-if="loading"
                            class="w-12 mx-auto" />
                        <ul
                            v-if="!loading"
                            class="max-h-[calc(100dvh-10rem)] divide-y divide-gray-200 overflow-y-auto">
                            <li
                                v-for="book in hasSearch ? filteredBooks : recent"
                                :key="book.id">
                                <div class="flex items-center gap-4 p-2 ">
                                    <div class="aspect-book w-16 shrink-0 shadow-sm rounded-sm overflow-hidden">
                                        <img
                                            :src="book.cover ?? DefaultCover"
                                            :alt="`Book cover image for ${book.title}`"
                                            class="size-full bg-gray-200 object-cover">
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <h3 class="font-serif">
                                            {{ book.title }}
                                        </h3>
                                        <p class="text-sm text-gray-500">
                                            By {{ book.authors ? book.authors.join(', ') : 'Unknown Author' }}
                                        </p>
                                    </div>
                                    <div class="ml-auto flex items-center gap-2 px-2">
                                        <div
                                            v-if="adding.includes(book.identifier)"
                                            class="rounded-full border p-1 animate-spin border-gray-200 bg-gray-100 text-gray-600"
                                        >
                                            <Icon
                                                name="LoaderCircle"
                                                class="w-4"
                                            />
                                        </div>

                                        <Button
                                            v-if="addedIdentifiers.has(book.identifier)"
                                            @click="removeBookFromUser(book)">
                                            Remove
                                        </Button>

                                        <Select
                                            v-model="selectedStatuses[book.identifier]"
                                            @update:model-value="value => select(book, value)">
                                            <SelectTrigger class="w-full">
                                                <SelectValue placeholder="Add to library" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectGroup>
                                                    <SelectItem
                                                        v-for="status in possibleStatuses"
                                                        :key="status.value"
                                                        :value="status.value">
                                                        {{ status.label }}
                                                    </SelectItem>
                                                </SelectGroup>
                                            </SelectContent>
                                        </Select>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button">
                            Close
                        </Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
