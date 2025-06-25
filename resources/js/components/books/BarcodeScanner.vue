<template>
    <div class="relative">
        <video
            ref="video"
            class="rounded w-full border shadow"
            autoplay
            playsinline
            muted />
        <div
            v-if="scanning"
            class="absolute top-0 left-0 w-full h-full bg-black/50 flex items-center justify-center text-white">
            <span>Scanning...</span>
        </div>
        <button
            class="mt-4 bg-blue-600 text-white px-4 py-2 rounded"
            @click="startScan">
            Start Scan
        </button>
        <p
            v-if="result"
            class="mt-2">
            Scanned ISBN: <strong>{{ result }}</strong>
        </p>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute } from '@/composables/useRoute.js'
import { useRequest } from '@/composables/useRequest.js'
import { BrowserMultiFormatReader } from '@zxing/browser'

const result = ref(null)
const video = ref(null)
const scanning = ref(false)

let codeReader

async function startScan () {
    result.value = null
    scanning.value = true

    codeReader = new BrowserMultiFormatReader()

    try {
        const devices = await BrowserMultiFormatReader.listVideoInputDevices()
        const selectedDeviceId = devices[0]?.deviceId

        if (!selectedDeviceId) {
            throw new Error('No camera found')
        }

        await codeReader.decodeFromVideoDevice(selectedDeviceId, video.value, (output, _) => {
            if (output) {
                result.value = output.getText()

                useRequest(useRoute('api.books.test', result.value), 'GET')
                    .then(response => {
                        console.log('API response:', response)
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
