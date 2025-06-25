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

        const devices = await BrowserMultiFormatReader.listVideoInputDevices()
        devicesOutput.value = devices
        const selectedDeviceId = devices[0]?.deviceId

        if (!selectedDeviceId) {
            throw new Error('No camera found')
        }

        await codeReader.decodeFromConstraints(
            {
                video: {
                    facingMode: { exact: 'environment' } // <- forces rear camera
                }
            },
            video.value,
            (output) => {
                if (output) {
                    result.value = output.getText()

                    useRequest(useRoute('api.books.test', result.value), 'GET')
                        .then(response => {
                            console.log('API response:', response)
                            book.value = response
                            // Handle the API response as needed
                        })
                }
            }
        )
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
            class="rounded w-full border shadow"
            autoplay
            playsinline
            muted />
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
        <hr>
        {{ devicesOutput }}
        <hr>
        {{ book }}
        <hr>
    </div>
</template>
