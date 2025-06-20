import { usePage } from '@inertiajs/vue3'
import { useRoute } from '@/composables/useRoute'
import { useDevicePixelRatio } from '@vueuse/core'
import { ImageProps } from '@/types/image'

interface ImageTransformOptions {
    width?: number | string | null
    height?: number | string | null
    format?: string
    quality?: number | string | null
    flip?: 'h' | 'v' | 'hv'
    contrast?: number | string | null
    version?: number | string | null
    background?: string
    blur?: number | string | null
    scale?: boolean | string | null
    crop?: 'top' | 'bottom' | 'left' | 'right' | 'center'
    pixelate?: number | string | null
    rotate?: number | string | null
}

export function useImageTransform () {
    const { props } = usePage()
    const { pixelRatio } = useDevicePixelRatio()

    const cleanPath = (rawPath: string): string => {
        const storageUrl = props.app?.storage_url || ''
        let path = rawPath.replace(storageUrl, '')

        if (path.startsWith('/storage/')) path = path.slice(9)
        else if (path.startsWith('storage/')) path = path.slice(8)

        return path.startsWith('/') ? path.slice(1) : path
    }

    const baseOptions = (props: ImageProps): ImageTransformOptions => {
        const result: Record<string, string | number> = {}

        for (const key of [
            'width', 'height', 'format', 'quality', 'flip', 'contrast',
            'version', 'background', 'blur', 'scale', 'crop'
        ]) {
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

            if (key === 'crop' && (!props.width || !props.height)) {
                continue
            }

            let processed: string | number

            if (typeof val === 'boolean') {
                processed = val ? 'true' : 'false'
            } else if (typeof val === 'string') {
                processed = val.toLowerCase()
            } else {
                processed = val
            }

            result[key] = processed
        }

        return Object.keys(result).length > 0 ? result : null
    }

    const buildOptionsString = (options: ImageTransformOptions): string => {
        return Object.entries(options)
            .filter(([key, value]) => {
                if (key === 'crop') {
                    return options.width && options.height && value !== null && value !== undefined
                }

                return value !== null && value !== undefined && value !== '' && value !== 0 && value !== 'auto'
            })
            .map(([key, value]) => {
                if ((key === 'width' || key === 'height') && typeof value === 'number') {
                    value = Math.round(value * pixelRatio.value)
                }

                if (typeof value === 'boolean') return `${key}=${value ? 'true' : 'false'}`

                return `${key}=${String(value).toLowerCase()}`
            })
            .join(',')
    }

    const getImageUrl = (path: string, options: ImageTransformOptions = {}) => {
        const cleaned = cleanPath(path)
        const optString = buildOptionsString(options)

        if (!optString) {
            return `/storage/${cleaned}`
        }

        return useRoute('image.transform', {
            path: cleaned,
            options: optString
        })
    }

    const buildSrcSet = (props: ImageProps) => {
        if (!props.enableSrcset || props.height) return null

        let sizes = [...props.srcsetSizes ? props.srcsetSizes : [], props.srcsetMaxWidth as number]

        if (props.width) {
            const maxWidth = parseInt(props.width as string, 10)
            if (maxWidth > 0) {
                sizes.push(maxWidth)
                sizes = sizes.filter(size => size <= maxWidth)
            }
        }

        return sizes
            .map((width) => {
                const options = {
                    ...baseOptions(props),
                    width,
                    height: null
                }
                return `${getImageUrl(props.src, options)} ${width}w`
            })
            .filter(Boolean)
            .join(', ')
    }

    return {
        cleanPath,
        baseOptions,
        buildSrcSet,
        buildOptionsString,
        getImageUrl
    }
}
