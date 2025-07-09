import { UserBookStatus } from '@/enums/UserBookStatus'
import { UserPermission } from '@/enums/UserPermission'

export type User = {
    permissions: string[];
    name: string;
    username: string;
    email?: string;
    avatar?: string;
    email_verified: boolean,
    settings?: Record<string, any>;
    book_identifiers?: Record<string, UserBookStatus>;
}

export type UserPasskey = {
    id: number,
    name: string,
    last_used_at: string,
}
