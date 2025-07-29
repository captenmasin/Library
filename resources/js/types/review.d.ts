import { User } from '@/types/user'
import { Book } from '@/types/book'

export type Review = {
    id: number,
    uuid: string,
    title: string,
    content: string,
    book_id: number,
    user_id: number,
    created_at: string,
    rating?: {
        id?: number,
        value?: number,
    },
    user?: User,
    book?: Book
}
