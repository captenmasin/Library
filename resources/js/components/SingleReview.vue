<script setup lang="ts">
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
            class="mt-px" />
        <div
            class="prose prose-sm mt-2 max-w-none"
            v-html="useMarkdown(review.content)" />

        <div
            v-if="review.user"
            class="flex items-center gap-2.5 mt-4">
            <div>
                <Avatar class="overflow-hidden rounded-full size-10">
                    <AvatarImage
                        v-if="review.user?.avatar"
                        :src="getImageUrl(review.user?.avatar, { width: 28, height: 28, crop: 'center' })"
                        :alt="review.user?.name"
                    />
                    <AvatarFallback class="rounded-lg bg-secondary font-semibold text-secondary-foreground">
                        {{ getInitials(review.user?.name) }}
                    </AvatarFallback>
                </Avatar>
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
