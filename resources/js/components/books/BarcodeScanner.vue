<script setup>
import Icon from '@/components/Icon.vue'
import DefaultCover from '~/images/default-cover.svg'
import BarcodeScanned from '~/audio/barcode-scanned.mp3'
import HorizontalSkeleton from '@/components/books/HorizontalSkeleton.vue'
import { toast } from 'vue-sonner'
import { useSound } from '@vueuse/sound'
import { useVibrate } from '@vueuse/core'
import { useRoute } from '@/composables/useRoute'
import { useRequest } from '@/composables/useRequest'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { Button } from '@/components/ui/button/index.js'
import { BrowserMultiFormatReader } from '@zxing/browser'
import { useUserBookStatus } from '@/composables/useUserBookStatus.js'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index.js'

// refs for UI state
const video = ref(null)
const scanning = ref(false)
const result = ref(null)
const book = ref(null)
const loading = ref(false)

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } = useUserBookStatus()

let controls = null

const { play } = useSound(BarcodeScanned)
const { vibrate } = useVibrate({ pattern: [300, 100] })

// single shared reader instance
const codeReader = new BrowserMultiFormatReader()

// start scanning ------------------------------------------------------------
async function startScan () {
    play()

    // reset UI
    result.value = null
    book.value = null
    scanning.value = true

    try {
        controls = await codeReader.decodeFromConstraints(
            {
                video: { facingMode: { ideal: 'environment' } }
            },
            video.value,
            async (output) => {
                if (!output) return

                // we have a barcode!
                const identifier = output.getText()
                result.value = identifier

                // hit your API
                loading.value = true
                useRequest(useRoute('api.books.fetch_or_create', identifier), 'GET')
                    .then(response => {
                        if (response.book) {
                            book.value = response.book
                            loading.value = false
                        } else {
                            console.error('No book found for identifier:', identifier)
                            result.value = null
                            book.value = null
                            loading.value = false
                        }
                    }).catch(error => {
                        console.error('Error fetching book:', error)
                        toast.error('Error fetching book details')
                        loading.value = false
                        result.value = null
                        book.value = null
                    })

                play()
                vibrate()

                // stopScan()
            }
        )
    } catch (err) {
        console.error('Barcode scanning error:', err)
        scanning.value = false
    }
}

// stop scanning -------------------------------------------------------------
function stopScan () {
    scanning.value = false
    if (controls) {
        controls.stop() // stop the camera
    }
}

function select (book, status) {
    if (book?.identifier) {
        if (addedBookIdentifiers.value.has(book.identifier)) {
            updateStatus(book, status)
        } else {
            addBookToUser(book.identifier, status)
        }
    }
}

// cleanup if user navigates away --------------------------------------------
onBeforeUnmount(stopScan)

onMounted(() => {
    startScan()
})
</script>

<template>
    <div class="relative">
        <!-- mirrored only on front cam -->
        <div
            v-show="scanning"
            class="relative">
            <video
                ref="video"
                class="rounded shadow w-full mx-auto"
                autoplay
                playsinline
                muted
            />
            <div class="w-full h-[2px] bg-red-500 opacity-75 shadow-xl shadow-red-500 absolute top-1/2 left-0 animate-scan" />
        </div>

        <div
            v-if="!scanning"
            class="flex items-center justify-center">
            <Button
                variant="secondary"
                @click="startScan">
                <Icon
                    name="ScanBarcode"
                    class="w-4" />
                Start Scanning
            </Button>
        </div>

        <div
            v-if="result"
            class="mt-2 text-sm bg-muted border-primary/10 text-primary rounded p-2 border-2 font-mono">
            Scanned code: <strong>{{ result }}</strong>
        </div>

        <HorizontalSkeleton
            v-if="loading"
            class="mt-4"
            :with-actions="false"
        />

        <div
            v-if="book && !loading"
            class="mt-4">
            <div class="flex items-center gap-4 py-2">
                <component
                    :is="book.links.show ? 'a' : 'span'"
                    target="_blank"
                    :href="book.links.show ? book.links.show : null">
                    <div class="shrink-0 overflow-hidden rounded-sm shadow-sm aspect-book w-22">
                        <img
                            :src="book.cover ?? DefaultCover"
                            :alt="`Book cover image for ${book.title}`"
                            class="bg-gray-200 object-cover size-full">
                    </div>
                </component>
                <div class="flex flex-col">
                    <div class="flex">
                        <component
                            :is="book.links.show ? 'a' : 'span'"
                            target="_blank"
                            :href="book.links.show ? book.links.show : null">
                            <h3 class="font-serif text-lg/6">
                                {{ book.title }}
                            </h3>
                        </component>
                    </div>
                    <p class="text-sm mt-0.5 text-muted-foreground">
                        By {{ book.authors?.map((a) => a.name).join(', ') }}
                    </p>
                    <p
                        v-if="book.description"
                        class="mt-1 text-xs text-muted-foreground line-clamp-2">
                        {{ book.description_clean }}
                    </p>
                </div>
            </div>
            <div class="ml-auto flex shrink-0 items-center justify-end gap-2 w-78">
                <div
                    v-if="addingBooks.includes(book.identifier)"
                    class="animate-spin rounded-full border border-gray-200 bg-gray-100 p-1 text-gray-600">
                    <Icon
                        name="LoaderCircle"
                        class="w-4"
                    />
                </div>

                <Button
                    v-if="addedBookIdentifiers.has(book.identifier)"
                    @click="removeBookFromUser(book)">
                    Remove
                </Button>

                <div class="w-44">
                    <Select
                        v-model="selectedStatuses[book.identifier]"
                        @update:model-value="value => select(book, value)">
                        <SelectTrigger class="w-full">
                            <SelectValue placeholder="Add to library" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectGroup>
                                <SelectItem
                                    v-for="status in possibleStatuses"
                                    :key="status.value"
                                    :value="status.value">
                                    {{ status.label }}
                                </SelectItem>
                            </SelectGroup>
                        </SelectContent>
                    </Select>
                </div>
            </div>
        </div>
    </div>
</template>
