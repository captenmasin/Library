import { UserBookStatus } from '@/enums/UserBookStatus'
import { Book } from '@/types/book'

export type Note = {
    id: number,
    content: string,
    created_at: string,
    updated_at: string,
    status: UserBookStatus,
    book?: Book
}
