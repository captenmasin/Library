import { computed } from 'vue'
import type { Book, BookApiResult } from '@/types/book'

export function useBook (book: Book | BookApiResult) {
    const userRating = computed(() => {
        if ('user_rating' in book) {
            return book.user_rating
        }

        return null
    })

    const hasUserRating = computed(() => {
        return !!userRating.value
    })

    return {
        userRating,
        hasUserRating
    }
}
