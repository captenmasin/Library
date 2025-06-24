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
        class="group flex flex-col rounded-md overflow-hidden shadow-sm"
        :style="{
            backgroundColor: book.colour,
            viewTransitionName: `book-cover-${book.identifier}`
        }"
        :class="[useContrast(book.colour, 'text-zinc-900', 'text-white')]"
    >
        <Link :href="book.links.show">
            <div class="w-full aspect-book relative overflow-hidden">
                <Image
                    v-if="book.cover"
                    :src="book.cover"
                    :height="315"
                    :width="200"
                    class="h-full w-full object-cover"
                />
            </div>
            <!--            <div class="p-2 font-serif italic">-->
            <!--                {{ book.title }}-->
            <!--            </div>-->
            <!--            <div>-->
            <!--                {{ book.authors.map(author => author.name).join(', ') }}s-->
            <!--            </div>-->
        </Link>
    </div>
</template>
