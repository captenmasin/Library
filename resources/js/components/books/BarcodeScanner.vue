<script setup>
import BarcodeScanned from '~/audio/barcode-scanned.mp3'
import { useSound } from '@vueuse/sound'
import { onBeforeUnmount, ref } from 'vue'
import { useRoute } from '@/composables/useRoute'
import { useRequest } from '@/composables/useRequest'
import { BrowserMultiFormatReader } from '@zxing/browser'

// refs for UI state
const video = ref(null)
const scanning = ref(false)
const result = ref(null)
const book = ref(null)

// single shared reader instance
const codeReader = new BrowserMultiFormatReader()

// start scanning ------------------------------------------------------------
async function startScan () {
    // reset UI
    result.value = null
    book.value = null
    scanning.value = true

    try {
        await codeReader.decodeFromConstraints(
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

                const { play } = useSound(BarcodeScanned)
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
    codeReader?.reset()
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
            :disabled="scanning"
            @click="startScan">
            {{ scanning ? 'Scanning…' : 'Start Scan' }}
        </button>

        <p
            v-if="result"
            class="mt-2">
            Scanned code: <strong>{{ result }}</strong>
        </p>

        <pre v-if="book">{{ book }}</pre>
    </div>
</template>
