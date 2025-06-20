<script setup lang="ts">
import PlaceholderImage from '~/images/placeholder.svg'
import { computed, ref } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { ImageProps } from '@/types/image'
import { useRoute } from '@/composables/useRoute'
import { useDevicePixelRatio } from '@vueuse/core'

const props = withDefaults(defineProps<ImageProps>(), {
    alt: '',
    enableSrcset: false,
    scale: true,
    showPlaceholder: true,
    srcsetSizes: () => [320, 640, 960],
    srcsetMaxWidth: 1280,
    placeholder: PlaceholderImage
})

const optionKeys = [
    'width',
    'height',
    'format',
    'quality',
    'flip',
    'crop',
    'pixelate',
    'contrast',
    'rotate',
    'version',
    'background',
    'scale',
    'blur'
]

const { pixelRatio } = useDevicePixelRatio()

const isLoaded = ref(false)

function handleLoad () {
    isLoaded.value = true
}

function handleError (event: Event) {
    const img = event.target as HTMLImageElement
    img.src = props.placeholder
    img.srcset = ''
}

function buildOptionsString (options: Record<string, any> | null): string | null {
    if (options === null || Object.keys(options).length === 0) {
        return null
    }

    return Object.entries(options)
        .map((option) => {
            const [key, value] = option
            if (value == null || value === '' || value === 0 || value === 'auto') {
                return null
            }
            const normalized = typeof value === 'string' ? value.toLowerCase() : value
            const devicePixelFixed = (key === 'width' || key === 'height') && !isNaN(parseFloat(normalized))
                ? Math.round(Number(normalized) * pixelRatio.value)
                : normalized

            return `${key}=${devicePixelFixed}`
        })
        .filter(Boolean)
        .join(',')
}

const baseOptions = computed(() => {
    const result: Record<string, string | number> = {}

    for (const key of optionKeys) {
        const val = props[key as keyof typeof props]

        if (
            val === null ||
            val === undefined ||
            val === '' ||
            val === 0 ||
            val === 'auto'
        ) {
            continue
        }

        // Only include crop if width and height are both present
        if (key === 'crop' && (!props.width || !props.height)) {
            continue
        }

        if (typeof val === 'boolean') {
            result[key] = val ? 'true' : 'false'
        } else if (typeof val === 'string') {
            result[key] = val.toLowerCase()
        } else if (typeof val === 'number') {
            result[key] = val
        }
    }

    return Object.keys(result).length > 0 ? result : null
})

const cleanSrc = computed(() => {
    const storageUrl = usePage().props?.app?.storage_url || ''
    let src = props.src.replace(storageUrl, '')

    if (src.startsWith('/storage/')) {
        src = src.slice(9)
    } else if (src.startsWith('storage/')) {
        src = src.slice(8)
    }

    return src.startsWith('/') ? src.slice(1) : `${src}`
})

function buildTransformUrl (options: Record<string, any> | null): string {
    return useRoute('image.transform', {
        path: cleanSrc.value,
        options: buildOptionsString(options)
    })
}

const finalSrc = computed(() => {
    return baseOptions.value ? buildTransformUrl(baseOptions.value) : `/storage/${cleanSrc.value}`
})

const srcset = computed(() => {
    if (!props.enableSrcset) {
        return null
    }
    if (props.height) {
        return null
    }

    let sizes = [...props.srcsetSizes, props.srcsetMaxWidth]

    if (props.width) {
        const maxWidth = props.width ? parseInt(props.width as string, 10) : 0
        if (maxWidth > 0) {
            sizes.push(maxWidth)
            sizes = sizes.filter(size => size <= maxWidth)
        }
    }

    return sizes
        .map((width) => {
            const options = {
                ...baseOptions.value,
                width,
                height: null
            }
            return `${buildTransformUrl(options)} ${width}w`
        })
        .filter(Boolean)
        .join(', ')
})
</script>

<template>
    <img
        :srcset="srcset"
        :src="finalSrc"
        :width="width"
        :sizes="enableSrcset && srcset?.length ? `(max-width: ${width || srcsetMaxWidth}px) 100vw, ${width || srcsetMaxWidth}px` : null"
        :height="height"
        loading="lazy"
        decode="async"
        class="transition-opacity"
        :class="isLoaded ? '' : 'animate-pulse'"
        :style="isLoaded ? null : {
            backgroundImage: showPlaceholder && placeholder && !isLoaded ? `url(${placeholder})` : '',
            backgroundSize: 'cover',
            backgroundPosition: 'center',
            backgroundColor: background ? `#${background}` : null,
        }"
        :alt="alt"
        @error="handleError"
        @load="handleLoad">
</template>
