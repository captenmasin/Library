import { Author } from '@/types/author'

export type Book = {
    id: number,
    title: string,
    cover: string,
    description: string,
    is_read: boolean,
    authors: Author[],
    colour?: string,
    has_custom_cover?: boolean,
}
