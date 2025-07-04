import { UserBookStatus } from '@/enums/UserBookStatus'

export type User = {
    name: string;
    username: string;
    email?: string;
    avatar?: string;
    email_verified: boolean,
    settings?: Record<string, any>;
    book_identifiers?: Record<string, UserBookStatus>;
}
