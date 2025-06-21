import { Author } from '@/types/author'

export type Book = {
    uuid: string,
    links: {
        show: string,
    },
    title: string,
    cover: string,
    description: string,
    authors: Author[],
    colour?: string,
    has_custom_cover?: boolean,
}
