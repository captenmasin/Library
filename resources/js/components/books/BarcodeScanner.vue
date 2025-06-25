<script setup>
import { ref } from 'vue'
import { useRoute } from '@/composables/useRoute.js'
import { useRequest } from '@/composables/useRequest.js'
import { BrowserMultiFormatReader } from '@zxing/browser'

const devicesOutput = ref(null)

const result = ref(null)
const book = ref(null)
const video = ref(null)
const scanning = ref(false)

let codeReader

async function startScan () {
    result.value = null
    scanning.value = true

    const codeReader = new BrowserMultiFormatReader()

    try {
        await navigator.mediaDevices.getUserMedia({ video: true })

        await codeReader.decodeFromVideoDevice(undefined, video.value, (output, _) => {
            if (output) {
                result.value = output.getText()

                useRequest(useRoute('api.books.test', result.value), 'GET').then((response) => {
                    console.log('API response:', response)
                    book.value = response
                    // Handle the API response as needed
                })

                stopScan()
                // emit to parent or make API call to auto-fill
            }
        })
    } catch (err) {
        console.error('Barcode scanning error:', err)
        scanning.value = false
    }
}

function stopScan () {
    scanning.value = false
    if (codeReader) {
        codeReader.reset()
    }
}
</script>

<template>
    <div class="relative">
        <video
            ref="video"
            class="w-full rounded border shadow"
            autoplay
            playsinline
            muted />
        <button
            class="mt-4 rounded bg-blue-600 px-4 py-2 text-white"
            @click="startScan">
            Start Scan
        </button>
        <p
            v-if="result"
            class="mt-2">
            Scanned ISBN: <strong>{{ result }}</strong>
        </p>
        <hr>
        {{ devicesOutput }}
        <hr>
        {{ book }}
        <hr>
    </div>
</template>
