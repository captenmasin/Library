// types/image.ts

export type ImageCrop = 'top' | 'bottom' | 'left' | 'right' | 'center'
export type ImageFormat = 'jpg' | 'jpeg' | 'png' | 'gif' | 'webp'
export type ImageFlip = 'h' | 'v' | 'hv'

export interface RequiredImageProps {
    src: string
}

export interface OptionalImageProps {
    alt?: string
    enableSrcset?: boolean
    srcsetSizes?: number[]
    srcsetMaxWidth?: number | string | null
    blur?: number | string | null
    scale?: boolean | string | null
    crop?: ImageCrop | null
    quality?: number | string | null
    contrast?: number | string | null
    version?: number | string | null
    format?: ImageFormat | null
    flip?: ImageFlip | null
    background?: string
    width?: number | string | null
    height?: number | string | null
    placeholder?: string
}

export type ImageProps = RequiredImageProps & OptionalImageProps
