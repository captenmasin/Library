import { Author } from '@/types/author'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { Review } from '@/types/review'
import { Publisher } from '@/types/publisher'

export type Book = {
    path: string,
    identifier: string,
    links: {
        show: string,
    },
    title: string,
    cover: string,
    description: string,
    description_clean: string,
    authors?: Author[],
    publisher?: Publisher,
    user_status: UserBookStatus,
    user_review: Review,
    page_count: number,
    user_tags: string[],
    colour?: string,
    imported?: boolean,
    in_library: boolean,
    has_custom_cover?: boolean,
    published_date: string,
}

export type BookApiResult = {
    codes: { type: string; identifier: string }[]
    identifier: string
    title: string
    pageCount?: number
    categories?: string[]
    publisher?: string
    description?: string
    authors?: string[]
    publishedDate?: string
    cover?: string
    service: string
    link: string
}
