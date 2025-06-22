import { Book } from '@/types/book'
import { useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { UserBookStatus } from '@/enums/UserBookStatus'

export function useUserBookStatus () {
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

        statusForm.patch(useRoute('users.books.update_status', book), {
            preserveScroll: true
        })
    }

    return {
        possibleStatuses,
        updateStatus
    }
}
