import { Book, BookApiResult } from '@/types/book'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { useRequest } from '@/composables/useRequest'
import { computed, onMounted, ref } from 'vue'

export function useUserBookStatus () {
    type StatusMap = Record<string, UserBookStatus>

    const page = usePage()
    const addedBooks = ref<Record<string, UserBookStatus | string>>({ ...page.props.auth.user_books })
    const selectedStatuses = ref<StatusMap>({})
    const addingBooks = ref<string[]>([])
    const addedBookIdentifiers = computed(() =>
        new Set(Object.keys(usePage().props.auth.user_books || {}))
    )

    const possibleStatuses = Object.entries(UserBookStatus).map(([key, value]) => ({
        value: key,
        label: value
    }))

    function updateStatus (book: Book | BookApiResult, status: string) {
        if (!possibleStatuses.some(s => s.value === status)) {
            throw new Error(`Invalid status: ${status}`)
        }

        const statusForm = useForm({
            status
        })

        statusForm.patch(useRoute('library.update_status', book), {
            preserveScroll: true
        })
    }

    async function addBookToUser (identifier: string, status: UserBookStatus) {
        const form = useForm({
            identifier: '',
            status: 'PlanToRead'
        })

        addingBooks.value.push(identifier)
        try {
            const response = await useRequest(useRoute('api.books.fetch_or_create', identifier), 'GET')

            if (response?.book?.identifier) {
                const book = response.book
                form.identifier = book.identifier
                form.status = status || 'PlanToRead'
                form.post(useRoute('library.store'), {
                    preserveScroll: true,
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

    async function removeBookFromUser (book: Book | BookApiResult) {
        router.delete(useRoute('library.destroy', book.identifier), {
            preserveScroll: true,
            onSuccess: () => {
                delete addedBooks.value[book.identifier]
                delete selectedStatuses.value[book.identifier]
            }
        })

        // useRequest(useRoute('api.books.fetch_or_create', book.identifier), 'GET')
        //     .then((response) => {
        //         const fetchedBook = response.book
        //
        //         if (fetchedBook) {
        //             router.delete(useRoute('library.destroy', fetchedBook), {
        //                 preserveScroll: true,
        //                 onSuccess: () => {
        //                     delete addedBooks.value[fetchedBook.identifier]
        //                     delete selectedStatuses.value[fetchedBook.identifier]
        //                 }
        //             })
        //         } else {
        //             console.error('Fetched book data is missing:', response)
        //         }
        //     })
        //     .catch((error) => {
        //         console.error('Error fetching book by identifier:', error)
        //     })
    }

    onMounted(() => {
        selectedStatuses.value = {
            ...(addedBooks.value as unknown as Record<string, UserBookStatus>)
        }
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
