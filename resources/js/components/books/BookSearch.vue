<script setup>
import Icon from '@/components/Icon.vue'
import Loader from '@/components/Loader.vue'
import { computed, ref } from 'vue'
import { watchDebounced } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { useForm, usePage } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input/index.js'
import { Button } from '@/components/ui/button/index.js'
import { useRequest } from '@/composables/useRequest.js'
import { DialogClose, DialogTitle } from '@/components/ui/dialog/index.js'
import { Dialog, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog'

const query = ref('')
const recent = ref([])
const filteredBooks = ref([])
const loading = ref(false)

const added = ref(usePage().props.auth.user_books)
const adding = ref([])

const form = useForm({
    identifier: ''
})

async function fetchOrCreateBook (identifier) {
    adding.value.push(identifier)
    try {
        const response = await useRequest(useRoute('api.books.create_or_fetch'), 'POST', { identifier })

        if (response?.data?.id) {
            const book = response.data
            form.identifier = book.identifier
            form.post(useRoute('users.books.store'), {
                onSuccess: () => {
                    added.value.push(book.identifier)
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

async function searchBooks () {
    if (!query.value) {
        filteredBooks.value = []
        return
    }
    loading.value = true
    useRequest(useRoute('api.books.search', { q: query.value }), 'GET').then((response) => {
        filteredBooks.value = response
    }).catch((error) => {
        console.error('Error searching for books:', error)
        loading.value = false
        filteredBooks.value = []
    }).finally(() => {
        loading.value = false
    })
}

watchDebounced(query, searchBooks, { debounce: 500 })

function removeBookFromAdded (identifier) {
    added.value = added.value.filter((id) => id !== identifier)
}

function select (book) {
    if (book?.identifier) {
        if (added.value.includes(book.identifier)) {
            useRequest(useRoute('api.books.fetch_by_identifier', { identifier: book.identifier }), 'GET')
                .then((response) => {
                    const fetchedBook = response.data
                    if (fetchedBook) {
                        form.identifier = fetchedBook.identifier

                        form.delete(useRoute('users.books.destroy', fetchedBook), {
                            onSuccess: () => {
                                removeBookFromAdded(fetchedBook.identifier)
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
            return
        }
        fetchOrCreateBook(book.identifier)
    }
}
</script>

<template>
    <div>
        <Dialog>
            <DialogTrigger as-child>
                <Button> Search</Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle> Add a book</DialogTitle>

                <Input
                    v-model="query"
                    class="w-full"
                    placeholder="Search for books by title, author, or publisher" />

                <Loader v-if="loading" />

                <ul
                    v-if="!loading"
                    class="max-h-96 divide-y divide-gray-200 overflow-y-auto">
                    <li
                        v-for="book in query === '' ? recent : filteredBooks"
                        :key="book.id">
                        <div
                            class="flex cursor-pointer items-center gap-4 p-2 hover:bg-gray-100"
                            @click="select(book)">
                            <img
                                :src="book.cover"
                                :alt="`Book cover image for ${book.title}`"
                                class="h-24 w-16 rounded-sm">
                            <div>
                                {{ book.title }}
                                <br>
                                By {{ book.authors ? book.authors.join(', ') : 'Unknown Author' }}
                            </div>
                            <div class="ml-auto px-2">
                                <div
                                    :class="{
                                        'opacity-100': added.includes(book.identifier) || adding.includes(book.identifier),
                                        'opacity-0': !added.includes(book.identifier) && !adding.includes(book.identifier),
                                        'border-green-200 bg-green-100 text-green-600':
                                            added.includes(book.identifier) && !adding.includes(book.identifier),
                                        'animate-spin border-gray-200 bg-gray-100 text-gray-600':
                                            !added.includes(book.identifier) && adding.includes(book.identifier),
                                    }"
                                    class="rounded-full border p-1"
                                >
                                    <Icon
                                        v-if="!added.includes(book.identifier) && adding.includes(book.identifier)"
                                        name="LoaderCircle"
                                        class="w-4"
                                    />

                                    <Icon
                                        v-if="added.includes(book.identifier) && !adding.includes(book.identifier)"
                                        name="Check"
                                        class="w-4" />
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>

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
