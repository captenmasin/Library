<script setup lang="ts">
import Image from '@/components/Image.vue'
import { PropType } from 'vue'
import { Book } from '@/types/book'
import { Link } from '@inertiajs/vue3'
import { useColours } from '@/composables/useColours'
import { useContrast } from '@/composables/useContrast'

const props = defineProps({
    books: {
        type: Array as PropType<Book[]>,
        required: true
    }
})

const { changeColourOpacity } = useColours()

const maxHeight = 36
const minHeight = 28

function seededRandom (seed: number): number {
    const x = Math.sin(seed) * 10000
    return x - Math.floor(x)
}

function getHeight (index: number): number {
    const seed = index + 1
    const rand = seededRandom(seed)
    return Math.floor(rand * (maxHeight - minHeight + 1)) + minHeight
}

function isTilted (index: number): boolean {
    const seed = index + 1
    const rand = seededRandom(seed)
    if (index > 0 && isTilted(index - 1)) {
        return false
    }

    return rand < 0.4 // 20% chance to tilt
}
</script>
<template>
    <ul class="flex items-end flex-wrap">
        <li
            v-for="(book, index) in books"
            :key="book.identifier"
            class="mb-8 border-b-2 group border-amber-900 pb-[2px]">
            <Link
                :href="book.links.show"
                prefetch
                :data-tilted="isTilted(index)"
                :style="{
                    backgroundColor: book.colour,
                    '--book-height': getHeight(index),
                    '--book-width': 7
                }"
                :class="{
                    'ml-[calc(var(--spacing)*(var(--book-width))/2)] mr-4 -translate-y-[3px] -rotate-14 group-hover:rotate-0 group-hover:translate-y-[2px]': isTilted(index)
                }"
                class="w-[calc(var(--spacing)*(var(--book-width)))] rounded-t-xs border border-background overflow-hidden group-hover:scale-125 group-hover:z-20 transition-all relative origin-center h-[calc(var(--spacing)*(var(--book-height)))] flex p-2">
                <div
                    :class="useContrast(book.colour, 'text-zinc-900/100', 'text-white/100')"
                    class="absolute px-4 top-0 z-2 left-0 h-full w-full">
                    <div class="absolute bottom-0 left-0 w-[calc(var(--spacing)*(var(--book-width)))] h-20 z-2 overflow-hidden">
                        <div
                            class="absolute top-0 left-0 w-[calc(var(--spacing)*(var(--book-width)))] h-full z-2 overflow-hidden"
                            :style="{backgroundImage: `linear-gradient(to bottom, ${changeColourOpacity(book.colour, 1)}, rgba(0, 0, 0, 0))`}"
                        />
                        <Image
                            :src="book.cover"
                            :alt="book.title"
                            :width="45"
                            :height="90"
                            class="w-full h-full object-cover opacity-65" />
                    </div>
                    <div class="pl-2.5 line-clamp-1 pr-1.5 rotate-[0.25turn] font-semibold translate-x-[calc(var(--spacing)*(var(--book-width)))] items-center flex w-[calc(var(--spacing)*(var(--book-height)))] h-[calc(var(--spacing)*(var(--book-width)))] z-10 absolute left-0 origin-top-left">
                        <h3 class="line-clamp-1 w-full font-serif  overflow-hidden text-ellipsis text-xs">
                            {{ book.title }}
                        </h3>
                    </div>
                </div>
            </Link>
        </li>
    </ul>
</template>
