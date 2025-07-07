import { ActivityType } from '@/enums/ActivityType'

export type Activity = {
    id: number,
    type: ActivityType,
    description: string,
    created_at: string,
}
