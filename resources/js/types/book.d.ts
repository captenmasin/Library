import { Author } from '@/types/author'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { Review } from '@/types/review'

export type Book = {
    path: string,
    identifier: string,
    links: {
        show: string,
    },
    title: string,
    cover: string,
    description: string,
    authors: Author[],
    user_status: UserBookStatus | null,
    user_review: Review | null,
    colour?: string,
    in_library: boolean,
    has_custom_cover?: boolean,
}
