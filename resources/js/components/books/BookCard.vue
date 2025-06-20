<script setup lang="ts">
import Image from '@/components/Image.vue'
import type { PropType } from 'vue'
import { Link } from '@inertiajs/vue3'
import type { Book } from '@/types/book'
import { useRoute } from '@/composables/useRoute'
import { useContrast } from '@/composables/useContrast'

defineProps({
    book: {
        type: Object as PropType<Book>,
        required: true
    }
})
</script>

<template>
    <div
        class="group flex h-full flex-col book-card rounded-md overflow-hidden shadow-xs"
        :style="{ backgroundColor: book.colour }"
        :class="[useContrast(book.colour, 'text-zinc-900', 'text-white')]"
    >
        <Link :href="useRoute('books.edit', book)">
            <div class="w-full aspect-[10/16] relative overflow-hidden">
                <Image
                    :src="book.cover"
                    :height="355"
                    :width="200"
                    class="h-full w-full object-cover"
                />
            </div>
            <div class="p-2">
                {{ book.title }}
            </div>
        </Link>
    </div>
</template>
