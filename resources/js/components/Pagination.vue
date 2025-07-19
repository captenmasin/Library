<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { cn } from '@/lib/utils'
import { PropType } from 'vue'

interface Meta {
  current_page: number
  last_page: number
  path: string
}

const props = defineProps({
  meta: {
    type: Object as PropType<Meta>,
    required: true,
  }
})

function pageUrl (page: number) {
  const params = new URLSearchParams(window.location.search)
  if (page > 1) {
    params.set('page', String(page))
  } else {
    params.delete('page')
  }
  const query = params.toString()
  return `${props.meta.path}${query ? '?' + query : ''}`
}
</script>

<template>
  <nav v-if="props.meta.last_page > 1" class="mt-4 flex justify-center">
    <ul class="inline-flex items-center gap-1 text-sm">
      <li>
        <Link
          :href="pageUrl(props.meta.current_page - 1)"
          preserve-scroll
          preserve-state
          :class="cn('px-3 py-1 rounded-md', props.meta.current_page === 1 ? 'pointer-events-none opacity-50' : 'hover:bg-muted')"
        >
          Previous
        </Link>
      </li>
      <li v-for="page in props.meta.last_page" :key="page">
        <Link
          :href="pageUrl(page)"
          preserve-scroll
          preserve-state
          :class="cn('px-3 py-1 rounded-md', page === props.meta.current_page ? 'bg-primary text-primary-foreground' : 'hover:bg-muted')"
        >
          {{ page }}
        </Link>
      </li>
      <li>
        <Link
          :href="pageUrl(props.meta.current_page + 1)"
          preserve-scroll
          preserve-state
          :class="cn('px-3 py-1 rounded-md', props.meta.current_page === props.meta.last_page ? 'pointer-events-none opacity-50' : 'hover:bg-muted')"
        >
          Next
        </Link>
      </li>
    </ul>
  </nav>
</template>
