import { Author } from '@/types/author'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { Review } from '@/types/review'
import { Publisher } from '@/types/publisher'
import { Note } from '@/types/note'

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
    tags?: string[],
    authors?: Author[],
    publisher?: Publisher,
    user_status: UserBookStatus | null,
    user_review: Review,
    user_rating?: {
        id: number,
        value: number
    },
    average_rating?: number,
    ratings_count?: number,
    page_count: number,
    user_notes?: Note[],
    user_tags: string[],
    colour: string,
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
    tags?: string[]
    publisher?: string
    description?: string
    description_clean?: string
    authors?: {
        uuid: string
        name: string
    }[]
    published_date?: string
    cover?: string
    service: string
    links: {
        show?: string
    }
}
