<script setup lang="ts">
import { PropType } from 'vue'
import { useTimeAgo } from '@vueuse/core'
import { Activity } from '@/types/activity'
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip'

defineProps({
    activity: {
        type: Object as PropType<Activity>,
        required: true
    }
})

function getTimeAgo (date: string | Date) {
    return useTimeAgo(new Date(date))
}
</script>

<template>
    <li
        class="flex flex-col justify-between gap-1 p-4 text-sm">
        <p
            class="text-secondary-foreground"
            v-html="activity.description" />
        <div class="flex shrink-0">
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger>
                        <p class="text-xs text-secondary-foreground/50">
                            {{ getTimeAgo(activity.created_at) }}
                        </p>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>
                            {{ new Date(activity.created_at).toLocaleString() }}
                        </p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
        </div>
    </li>
</template>
