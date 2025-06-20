<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { Button } from '@/components/ui/button/index.js';
import BookSearch from '@/components/books/BookSearch.vue';
import CameraScanner from '@/components/books/CameraScanner.vue';

const cameraDetails = ref({})
const rawResult = ref({})

const scannerEnabled = ref(false)
const barcodeForm = useForm({
    code: ''
})

const form = useForm({
    identifier: ''
})

function addBook () {
    form.post(useRoute('books.store'))
}

function fetchBookIdentifier () {
    barcodeForm.clearErrors()

    fetch(useRoute('books.api.barcode', { code: barcodeForm.code }))
        .then(response => response.json())
        .then(data => {
            if (data.errors) {
                barcodeForm.setError('code', data.errors.code)
            } else {
                form.identifier = data.identifier
                addBook()
            }
        })
}

function onDecode (result) {
    fetchBookIdentifier()
    scannerEnabled.value = false
}

function toggleScan () {
    scannerEnabled.value = !scannerEnabled.value
}
</script>

<template>
    <div>
        <div class="flex items-center gap-2">
            <BookSearch />
            or
            <Button
                type="button"
                @click="toggleScan">
                Scan
            </Button>
        </div>

        <CameraScanner
            v-model="barcodeForm.barcode"
            v-model:open-modal="scannerEnabled"
            v-model:camera-details="cameraDetails"
            v-model:raw-result="rawResult"
            @decoded="onDecode"
        />
    </div>
</template>
