import { Book } from '@/types/book'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { useRequest } from '@/composables/useRequest'
import { computed, onMounted, ref } from 'vue'

export function useUserBookStatus () {
    const page = usePage()
    const addedBooks = ref({ ...page.props.auth.user_books })
    const addingBooks = ref([])
    const selectedStatuses = ref({})
    const addedBookIdentifiers = computed(() =>
        new Set(Object.keys(usePage().props.auth.user_books || {}))
    )

    const possibleStatuses = Object.entries(UserBookStatus).map(([key, value]) => ({
        value: key,
        label: value
    }))

    function updateStatus (book: Book, status: string) {
        if (!possibleStatuses.some(s => s.value === status)) {
            throw new Error(`Invalid status: ${status}`)
        }

        const statusForm = useForm({
            status
        })

        statusForm.patch(useRoute('user.books.update_status', book), {
            preserveScroll: true
        })
    }

    async function addBookToUser (identifier, status) {
        const form = useForm({
            identifier: '',
            status: 'PlanToRead'
        })

        addingBooks.value.push(identifier)
        try {
            const response = await useRequest(useRoute('api.books.fetch_or_create'), 'POST', { identifier })

            if (response?.book?.identifier) {
                const book = response.book
                form.identifier = book.identifier
                form.status = status || 'PlanToRead'
                form.post(useRoute('user.books.store'), {
                    onSuccess: () => {
                        addedBooks.value[book.identifier] = form.status
                        addingBooks.value = addingBooks.value.filter((id) => id !== identifier)
                    }
                })
            } else {
                addingBooks.value = addingBooks.value.filter((id) => id !== identifier)
                form.setError('identifier', 'Failed to create or fetch book')
                console.error('Missing identifier in book data:', response)
            }
        } catch (error) {
            addingBooks.value = addingBooks.value.filter((id) => id !== identifier)
            form.setError('identifier', 'Failed to create or fetch book')
            console.error('Error creating/fetching book:', error)
        }
    }

    async function removeBookFromUser (book) {
        useRequest(useRoute('api.books.fetch_by_identifier', { identifier: book.identifier }), 'GET')
            .then((response) => {
                const fetchedBook = response.data
                if (fetchedBook) {
                    router.delete(useRoute('user.books.destroy', fetchedBook), {
                        onSuccess: () => {
                            delete addedBooks.value[fetchedBook.identifier]
                            delete selectedStatuses.value[fetchedBook.identifier]
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

    onMounted(() => {
        selectedStatuses.value = { ...addedBooks.value }
    })

    return {
        addedBooks,
        addingBooks,
        addBookToUser,
        removeBookFromUser,
        possibleStatuses,
        addedBookIdentifiers,
        selectedStatuses,
        updateStatus
    }
}
