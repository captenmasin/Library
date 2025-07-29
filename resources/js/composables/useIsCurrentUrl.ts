import { usePage } from '@inertiajs/vue3'

export function useIsCurrentUrl (url: string) {
    const page = usePage()

    let cleanedUrl = url.replace(page.props.app.url, '')
    if (cleanedUrl === '') {
        cleanedUrl = '/'
    }

    // remove parameters from the URL
    const cleanedPageUrl = page.url.split('?')[0]

    return cleanedPageUrl === cleanedUrl
}
