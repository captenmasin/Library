import { User } from '@/types/user'
import { Book } from '@/types/book'

export type Post = {
    uuid: string,
    path: string,
    title: string,
    content: string,
    featured_image?: string,
    user?: User,
    book?: Book,
    created_at: string,
    updated_at: string,
}
