import { User } from '@/types/user'
import { Book } from '@/types/book'

export type Review = {
    uuid: string,
    title: string,
    content: string,
    book_id: number,
    user_id: number,
    rating?: {
        id?: number,
        value?: number,
    },
    user?: User,
    book?: Book
}
