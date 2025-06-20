<script setup>
import barcodeScannedAudioMp3 from '~/audio/barcode-scanned.mp3'
import { StreamBarcodeReader } from '@teckel/vue-barcode-reader'
import { ref, reactive, computed, watch, onBeforeUnmount, onMounted } from 'vue'
import Icon from '@/components/Icon.vue';

const barcodeScannedAudio = ref(null)

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: null
    },
    openModal: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:modelValue', 'update:openModal', 'update:cameraDetails', 'update:rawResult', 'decoded'])

const initialState = {
    loaded: false,
    modalState: false,
    torch: false,
    zoom: 1,
    autofocus: true,
    focusDistance: 0,
    landscape: false,
    hasTorch: false,
    hasZoom: false,
    hasAutofocus: false,
    hasFocusDistance: false,
    videoDevices: {},
    deviceIndex: null,
    debounce: false,
    debounceTimeout: null,
    cameraDetails: {}
}

onMounted(() => {
    barcodeScannedAudio.value = new Audio(barcodeScannedAudioMp3)
})

const state = reactive({ ...initialState })

const isAndroidChrome = computed(() => state.isMobile && state.isAndroid && state.isChrome)

watch(
    () => props.openModal,
    (newVal) => {
        state.modalState = newVal
    }
)

onBeforeUnmount(() => {
    modalClose()
})

const onLoaded = () => {
    state.loaded = true
    if (!state.hasAutofocus) {
        state.autofocus = false
    }
    console.log('loaded')
}

const onDecode = (decodedText) => {
    barcodeScannedAudio.value.play()
    console.log('Barcode scanned:', decodedText)
    emit('update:modelValue', decodedText)
    emit('decoded', decodedText)
    modalClose()
}

const onResult = (result) => {
    console.log('Raw Result:', result)
    emit('update:rawResult', JSON.parse(JSON.stringify(result)))
}

const switchInputDevice = () => {
    const length = state.videoDevices?.devices?.length
    if (state.deviceIndex >= 0 && length > 1) {
        state.loaded = false
        state.deviceIndex = state.deviceIndex + 1 >= length ? 0 : state.deviceIndex + 1
    }
}

onMounted(() => {
    if (typeof navigator !== 'undefined') {
        state.isMobile = navigator?.userAgentData?.mobile || navigator?.platform === 'iPad' || navigator?.platform === 'iPhone'
        state.isAndroid = navigator?.userAgentData?.platform === 'Android'
        state.isChrome = navigator?.userAgentData?.brands.findIndex((brand) => brand.brand === 'Google Chrome' || brand.brand === 'Chromium') !== -1
    }
})

const sliderMovement = () => {
    if (!state.debounce) {
        state.debounce = true
        window.navigator?.vibrate?.(10)
        clearTimeout(state.debounceTimeout)
        state.debounceTimeout = setTimeout(() => {
            state.debounce = false
        }, 10)
    }
}

const modalClose = () => {
    emit('update:cameraDetails', state.cameraDetails)
    Object.assign(state, initialState)
    emit('update:openModal', false)
}
</script>

<template>
    <div
        v-if="state.modalState"
        class="modal-container">
        <div
            class="close"
            @click="modalClose">
            <Icon name="xmark" class="w-6" />
        </div>

        <div class="controls">
            <div v-if="state.loaded">
                <div
                    :class="{ disabled: state.videoDevices?.devices?.length < 2 }"
                    @click="switchInputDevice"
                >
                    <Icon name="arrow-right" class="w-6" />
                </div>
            </div>
            <div v-if="state.loaded">
                <div
                    :class="{ disabled: !state.hasAutofocus, activated: !state.autofocus && state.hasFocusDistance }"
                    @click="state.autofocus = !state.autofocus"
                >
                    <Icon name="eye" class="w-6" />
                </div>
            </div>
            <div v-if="state.loaded">
                <div
                    :class="{ disabled: !isAndroidChrome, activated: state.landscape }"
                    @click="state.landscape = !state.landscape"
                >
                    <Icon name="phone" class="w-6 rotate-90" />
                </div>
            </div>
            <div v-if="state.loaded">
                <div
                    :class="{ disabled: !state.hasTorch, activated: state.hasTorch && state.torch }"
                    @click="state.torch = !state.torch"
                >
                    <Icon name="bolt" class="w-6" />
                </div>
            </div>
        </div>
        <div class="barcode-container">
            <StreamBarcodeReader
                v-model:video-devices="state.videoDevices"
                v-model:has-focus-distance="state.hasFocusDistance"
                v-model:has-autofocus="state.hasAutofocus"
                v-model:has-torch="state.hasTorch"
                v-model:has-zoom="state.hasZoom"
                v-model:camera-details="state.cameraDetails"
                :landscape="state.landscape"
                :torch="state.torch"
                :zoom="Number(state.zoom)"
                :autofocus="state.autofocus"
                :focus-distance="Number(state.focusDistance)"
                :device-index="state.deviceIndex"
                :ms-between-decoding="500"
                @decode="onDecode"
                @loaded="onLoaded"
                @result="onResult"
            />
            <div
                v-if="!state.autofocus && state.hasFocusDistance && state.loaded"
                class="focus-container">
                <div>Focus</div>
                <input
                    v-model="state.focusDistance"
                    type="range"
                    :min="state.hasFocusDistance.min || 0"
                    :max="Math.min(state.hasFocusDistance.max, 1) || 1"
                    :step="state.hasFocusDistance.step || 0.1"
                    class="h-2 w-64 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                >
            </div>

            <div
                v-if="state.hasZoom && state.loaded"
                class="zoom-container">
                <div>Zoom</div>
                <input
                    v-model="state.zoom"
                    type="range"
                    :min="state.hasZoom.min || 1"
                    :max="state.hasZoom.max || 2"
                    :step="state.hasZoom.step || 0.1"
                    class="h-2 w-64 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                >
            </div>
        </div>
    </div>
</template>

<style>
.modal-container {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 200;
    background-color: black;
}

.controls {
    position: absolute;
    z-index: 1;
    bottom: 20px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: auto;
    padding: 20px;
}

.controls > div:not(.title) {
    flex-basis: 50px;
    min-width: 50px;
}

.controls > div:not(.title) > div, .close {
    position: relative;
    width: 40px;
    height: 40px;
    margin: auto;
    border-radius: 50%;
    color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(255, 255, 255, 0.25);
    transition: all ease-in-out 0.1s;
    cursor: pointer;
}

.controls > div:not(.title) > div:not(.close):hover {
    background-color: rgba(255, 255, 255, 0.5);
}

.controls > div > div.activated {
    color: black;
    background-color: white;
}

.controls > div > div.disabled {
    pointer-events: none;
}

div.close {
    top: 20px;
    right: 28px;
    position: absolute;
    color: #ef4444;
    background-color: #fef2f2;
    opacity: 0.75;
    transition: opacity 0.3s;
    cursor: pointer;
    z-index: 30;
}

div.close:hover {
    opacity: 1;
}

.controls > div > div.disabled:after {
    content: '';
    position: absolute;
    top: 15px;
    left: 15px;
    width: 32px;
    height: 32px;
    border-top: 1px solid white;
    transform: rotate(-45deg);
}

.barcode-container {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

video {
    max-width: 1000px !important;
}

.zoom-container {
    position: absolute;
    z-index: 3;
    top: 100px;
    bottom: 0;
    right: 20px;
    width: 55px;
    color: white;
}

.zoom-container > div {
    position: absolute;
    top: 0px;
    left: 0;
    right: 0;
    text-align: center;
}

.zoom-container input[type="range"] {
    transform: rotate(-90deg) translateX(-100%) translateY(-50%);
    transform-origin: 0 0;
    touch-action: none;
    position: absolute;
    top: 35px;
    left: 50%;
}

.focus-container {
    position: absolute;
    z-index: 3;
    top: 100px;
    bottom: 0;
    left: 20px;
    width: 55px;
    color: white;
}

.focus-container > div {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    font-size: 14px;
    text-align: center;
}

.focus-container input[type="range"] {
    transform: rotate(-90deg) translateX(-100%) translateY(-50%);
    transform-origin: 0 0;
    touch-action: none;
    position: absolute;
    top: 35px;
    left: 50%;
}
</style>
