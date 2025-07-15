<script setup lang="ts">
import UserAvatar from '@/components/UserAvatar.vue'
import StarRatingDisplay from '@/components/StarRatingDisplay.vue'
import { PropType } from 'vue'
import { cn } from '@/lib/utils'
import { Book } from '@/types/book'
import { Note } from '@/types/note'
import { Link } from '@inertiajs/vue3'
import { Review } from '@/types/review'
import { useDateFormat } from '@vueuse/core'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'
import { useRoute } from '@/composables/useRoute'
import { useMarkdown } from '@/composables/useMarkdown'
import { getInitials } from '@/composables/useInitials'
import { useImageTransform } from '@/composables/useImageTransform'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'

const props = defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    },
    review: {
        type: Object as PropType<Review>,
        required: true
    },
    class: {
        type: String,
        default: ''
    }
})

const { getImageUrl } = useImageTransform()

function formatDate (date) {
    return useDateFormat(date, 'Mo MMMM h:ma')
}
</script>

<template>
    <div :class="cn('group py-6', props.class)">
        <h3
            v-if="review.title"
            class="text-xl font-semibold">
            {{ review.title }}
        </h3>
        <StarRatingDisplay
            v-if="review.rating?.value"
            :rating="review.rating.value"
            class="mt-px mb-2" />
        <div
            class="prose prose-sm max-w-none"
            v-html="useMarkdown(review.content)" />

        <div
            v-if="review.user"
            class="flex items-center gap-2.5 mt-4">
            <div>
                <UserAvatar
                    :user="review.user"
                    :size="28"
                    class="size-10" />
            </div>
            <div class="flex flex-col gap-px">
                <Label>
                    {{ review.user?.name || 'Anonymous' }}
                </Label>
                <div class="text-xs font-semibold text-muted-foreground">
                    {{ formatDate(review.created_at) }}
                </div>
            </div>
        </div>
    </div>
</template>
