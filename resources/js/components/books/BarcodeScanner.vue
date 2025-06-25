<script setup>
import BarcodeScanned from '~/audio/barcode-scanned.mp3'
import { useSound } from '@vueuse/sound'
import { useForm } from '@inertiajs/vue3'
import { onBeforeUnmount, ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { useRequest } from '@/composables/useRequest'
import { Button } from '@/components/ui/button/index.js'
import { BrowserMultiFormatReader } from '@zxing/browser'
import { useUserBookStatus } from '@/composables/useUserBookStatus.js'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select/index.js'

// refs for UI state
const video = ref(null)
const scanning = ref(false)
const result = ref(null)
const book = ref(null)
const selectedStatus = ref(null)

const { possibleStatuses, updateStatus, selectedStatuses, addedBookIdentifiers, addingBooks, addBookToUser, removeBookFromUser } = useUserBookStatus()

let controls = null

const { play } = useSound(BarcodeScanned)

// single shared reader instance
const codeReader = new BrowserMultiFormatReader()

// start scanning ------------------------------------------------------------
async function startScan () {
    play()

    // reset UI
    result.value = null
    book.value = null
    scanning.value = true

    useRequest(useRoute('api.books.fetch_by_identifier', '9781367798540'), 'GET')
        .then(response => {
            book.value = response.data
        })

    try {
        controls = await codeReader.decodeFromConstraints(
            {
                video: { facingMode: { ideal: 'environment' } } // rear cam preferred
            },
            video.value,
            async (output) => {
                if (!output) return

                // we have a barcode!
                const raw = output.getText()
                result.value = raw

                // hit your API
                book.value = await useRequest(useRoute('api.books.test', raw), 'GET')

                play()

                stopScan() // tidy up
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
</script>

<template>
    <div class="relative">
        <!-- mirrored only on front cam -->
        <video
            ref="video"
            class="w-full rounded border shadow"
            autoplay
            playsinline
            muted
            :style="{ transform: result ? 'scaleX(1)' : 'scaleX(-1)' }"
        />

        <button
            class="mt-4 rounded bg-blue-600 px-4 py-2 text-white"
            @click="scanning ? stopScan() : startScan()">
            {{ scanning ? 'Scanning…' : 'Start Scan' }}
        </button>

        <p
            v-if="result"
            class="mt-2">
            Scanned code: <strong>{{ result }}</strong>
        </p>

        <div v-if="book">
            {{ selectedStatus }}
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
</template>
