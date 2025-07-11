import { Author } from '@/types/author'
import { UserBookStatus } from '@/enums/UserBookStatus'
import { Review } from '@/types/review'
import { Publisher } from '@/types/publisher'

export type Note = {
    id: number,
    content: string,
    created_at: string,
    updated_at: string,
    status: UserBookStatus
}
