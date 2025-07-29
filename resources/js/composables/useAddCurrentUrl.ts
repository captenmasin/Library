import { usePage } from '@inertiajs/vue3'

export function useAddCurrentUrl (url: string | null): string {
    if (!url) {
        return ''
    }

    const page = usePage()
    const currentUrl = page.props.currentUrl

    if (url && !url.includes('?')) {
        url += '?'
    } else if (url && !url.endsWith('?') && !url.endsWith('&')) {
        url += '&'
    } else if (!url) {
        url = ''
    }
    url += `src=${encodeURIComponent(currentUrl)}`

    return url
}
