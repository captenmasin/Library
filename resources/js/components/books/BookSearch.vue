<script setup>
import Loader from '@/components/Loader.vue'
import { nextTick, ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { watchDebounced } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { Input } from '@/components/ui/input/index.js'
import { Button } from '@/components/ui/button/index.js'
import { DialogClose } from '@/components/ui/dialog/index.js'
import { Dialog, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog'

const recent = []

const open = ref(false)
const query = ref('')
const filteredBooks = ref([])
const loading = ref(false)

const form = useForm({
    identifier: ''
})

function submitForm () {
    form.post(useRoute('books.store'))
}

watchDebounced(
    query,
    () => {
        if (query.value === '') {
            filteredBooks.value = []
        } else {
            loading.value = true
            fetch(useRoute('books.api.search', { q: query.value }))
                .then((response) => response.json())
                .then((data) => {
                    filteredBooks.value = data
                    loading.value = false
                })
        }
    },
    { debounce: 500 }
)

function select (book) {
    if (book) {
        form.identifier = book.identifier
        submitForm()
        open.value = false
        nextTick(() => {
            query.value = ''
        })
    }
}
</script>

<template>
    <div>
        <Dialog>
            <DialogTrigger> Search</DialogTrigger>
            <DialogContent>
                <Input
                    v-model="query"
                    class="w-full"
                    placeholder="Search for books by title, author, or publisher" />

                <Loader v-if="loading" />

                <ul
                    v-if="!loading"
                    class="max-h-96 divide-y divide-gray-200 overflow-y-auto">
                    <li
                        v-for="book in query === '' ? recent : filteredBooks"
                        :key="book.id">
                        <DialogClose as-child>
                            <Button
                                variant="link"
                                type="button"
                                @click="select(book)">
                                <img
                                    :src="book.cover"
                                    :alt="`Book cover image for ${book.title}`"
                                    class="h-24 w-16 rounded-sm">
                                {{ book.title }}
                            </Button>
                        </DialogClose>
                    </li>
                </ul>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button type="button">
                            Close
                        </Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!--        <TransitionRoot-->
        <!--            :show="open"-->
        <!--            as="template"-->
        <!--            appear-->
        <!--            @after-leave="query = ''">-->
        <!--            <Dialog-->
        <!--                class="relative z-10"-->
        <!--                @close="open = false">-->
        <!--                <TransitionChild-->
        <!--                    as="template"-->
        <!--                    enter="ease-out duration-300"-->
        <!--                    enter-from="opacity-0"-->
        <!--                    enter-to="opacity-100"-->
        <!--                    leave="ease-in duration-200"-->
        <!--                    leave-from="opacity-100"-->
        <!--                    leave-to="opacity-0">-->
        <!--                    <div class="fixed inset-0 bg-gray-500/25 transition-opacity" />-->
        <!--                </TransitionChild>-->

        <!--                <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">-->
        <!--                    <TransitionChild-->
        <!--                        as="template"-->
        <!--                        enter="ease-out duration-300"-->
        <!--                        enter-from="opacity-0 scale-95"-->
        <!--                        enter-to="opacity-100 scale-100"-->
        <!--                        leave="ease-in duration-200"-->
        <!--                        leave-from="opacity-100 scale-100"-->
        <!--                        leave-to="opacity-0 scale-95">-->
        <!--                        <DialogPanel class="mx-auto max-w-3xl transform overflow-hidden rounded-xl bg-white shadow-2xl ring-1 ring-black/5 transition-all divide-y divide-gray-100">-->
        <!--                            <Combobox-->
        <!--                                v-slot="{ activeOption }"-->
        <!--                                @update:model-value="onSelect">-->
        <!--                                <div class="grid grid-cols-1">-->
        <!--                                    <ComboboxInput-->
        <!--                                        class="col-start-1 row-start-1 h-12 w-full border-0 border-b-2 border-transparent pr-4 pl-11 text-base placeholder:text-gray-400 text-gray-900 outline-hidden focus:border-red-200 sm:text-sm"-->
        <!--                                        placeholder="Search..."-->
        <!--                                        @change="query = $event.target.value"-->
        <!--                                        @blur="query = ''" />-->
        <!--                                    <MagnifyingGlassIcon-->
        <!--                                        class="pointer-events-none col-start-1 row-start-1 ml-4 self-center text-gray-400 size-5"-->
        <!--                                        aria-hidden="true" />-->
        <!--                                </div>-->
        <!--                                <div class="py-2 px-4 text-xs">-->
        <!--                                    use <em class="font-mono font-semibold">inauthor:</em> to search by author, <em class="font-mono font-semibold">intitle:</em> to search by title, and <em class="font-mono font-semibold">inpublisher:</em> to search by publisher.-->
        <!--                                </div>-->

        <!--                                <Loader v-if="loading" />-->

        <!--                                <div v-if="!loading">-->
        <!--                                    <ComboboxOptions-->
        <!--                                        v-if="filteredBooks.length > 0"-->
        <!--                                        class="flex transform-gpu divide-x divide-gray-100"-->
        <!--                                        as="div"-->
        <!--                                        static-->
        <!--                                        hold>-->
        <!--                                        <div :class="['max-h-140 min-w-0 flex-auto scroll-py-4 overflow-y-auto px-6 py-4', activeOption && 'sm:h-140']">-->
        <!--                                            &lt;!&ndash;                                    <h2 v-if="query === ''" class="mt-2 mb-4 text-xs font-semibold text-gray-500">Recent searches</h2>&ndash;&gt;-->
        <!--                                            <div-->
        <!--                                                hold-->
        <!--                                                class="-mx-2 text-sm text-gray-700">-->
        <!--                                                <ComboboxOption-->
        <!--                                                    v-for="book in query === '' ? recent : filteredBooks"-->
        <!--                                                    :key="book.id"-->
        <!--                                                    v-slot="{ active }"-->
        <!--                                                    :value="book"-->
        <!--                                                    as="template">-->
        <!--                                                    <div :class="['group flex cursor-default select-none items-center rounded-md p-2', active && 'bg-gray-100 text-gray-900 outline-hidden']">-->
        <!--                                                        <img-->
        <!--                                                            loading="lazy"-->
        <!--                                                            :src="book.cover"-->
        <!--                                                            :alt="`Book cover image for ${book.title}`"-->
        <!--                                                            class="w-16 flex-none rounded-sm aspect-cover">-->
        <!--                                                        <div class="ml-3 flex w-full flex-col overflow-hidden">-->
        <!--                                                            <span class="flex-auto truncate text-lg font-semibold">{{ book.title }}</span>-->
        <!--                                                            <span-->
        <!--                                                                v-if="book.authors"-->
        <!--                                                                class="flex-auto truncate">{{ book.authors.join(', ') }}</span>-->
        <!--                                                            <span class="mt-1 flex-auto truncate text-xs">{{ book.publishedDate }}</span>-->
        <!--                                                        </div>-->
        <!--                                                        <ChevronRightIcon-->
        <!--                                                            v-if="active"-->
        <!--                                                            class="ml-3 flex-none text-gray-400 size-5"-->
        <!--                                                            aria-hidden="true" />-->
        <!--                                                    </div>-->
        <!--                                                </ComboboxOption>-->
        <!--                                            </div>-->
        <!--                                        </div>-->

        <!--                                        <div-->
        <!--                                            v-if="activeOption"-->
        <!--                                            class="hidden w-1/2 flex-none flex-col overflow-y-auto h-140 divide-y divide-gray-100 sm:flex">-->
        <!--                                            <div class="flex-none p-6 text-center">-->
        <!--                                                <img-->
        <!--                                                    :src="activeOption.cover"-->
        <!--                                                    alt=""-->
        <!--                                                    class="mx-auto w-28 rounded-md aspect-cover">-->
        <!--                                                <h2 class="mt-3 font-semibold text-gray-900">-->
        <!--                                                    {{ activeOption.title }}-->
        <!--                                                </h2>-->
        <!--                                                <p class="text-gray-500 text-sm/6">-->
        <!--                                                    {{ activeOption.role }}-->
        <!--                                                </p>-->
        <!--                                            </div>-->
        <!--                                            <div class="flex flex-auto flex-col justify-between p-6">-->
        <!--                                                <dl class="grid grid-cols-1 gap-x-6 gap-y-3 text-sm text-gray-700">-->
        <!--                                                    <template v-if="activeOption.publishedDate">-->
        <!--                                                        <dt class="col-end-1 font-semibold text-gray-900">-->
        <!--                                                            Published-->
        <!--                                                        </dt>-->
        <!--                                                        <dd>{{ activeOption.publishedDate }}</dd>-->
        <!--                                                    </template>-->

        <!--                                                    <template v-if="activeOption.authors">-->
        <!--                                                        <dt class="col-end-1 font-semibold text-gray-900">-->
        <!--                                                            Author/s-->
        <!--                                                        </dt>-->
        <!--                                                        <dd>{{ activeOption.authors.join(', ') }}</dd>-->
        <!--                                                    </template>-->

        <!--                                                    <template v-if="activeOption.description">-->
        <!--                                                        <dt class="col-end-1 font-semibold text-gray-900">-->
        <!--                                                            Description-->
        <!--                                                        </dt>-->
        <!--                                                        <dd>{{ activeOption.description }}</dd>-->
        <!--                                                    </template>-->
        <!--                                                </dl>-->
        <!--                                                <button-->
        <!--                                                    type="button"-->
        <!--                                                    class="mt-6 w-full rounded-md bg-primary px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"-->
        <!--                                                    @click="onSelect(activeOption)">-->
        <!--                                                    Add to shelf-->
        <!--                                                </button>-->
        <!--                                            </div>-->
        <!--                                        </div>-->
        <!--                                    </ComboboxOptions>-->

        <!--                                    <div-->
        <!--                                        v-if="query !== '' && filteredBooks.length === 0"-->
        <!--                                        class="px-6 py-14 text-center text-sm sm:px-14">-->
        <!--                                        <UsersIcon-->
        <!--                                            class="mx-auto text-gray-400 size-6"-->
        <!--                                            aria-hidden="true" />-->
        <!--                                        <p class="mt-4 font-semibold text-gray-900">-->
        <!--                                            No books found-->
        <!--                                        </p>-->
        <!--                                        <p class="mt-2 text-gray-500">-->
        <!--                                            We couldn’t find anything with that term. Please try again.-->
        <!--                                        </p>-->
        <!--                                    </div>-->
        <!--                                </div>-->
        <!--                            </Combobox>-->
        <!--                        </DialogPanel>-->
        <!--                    </TransitionChild>-->
        <!--                </div>-->
        <!--            </Dialog>-->
        <!--        </TransitionRoot>-->
    </div>
</template>
