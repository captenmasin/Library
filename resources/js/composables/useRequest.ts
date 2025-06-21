import { usePage } from '@inertiajs/vue3'

type RequestMethod = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';

type ApiResponse = any;

export async function useRequest<T = ApiResponse> (
    url: string,
    method: RequestMethod = 'POST',
    data: Record<string, any> = {}
): Promise<T> {
    const page = usePage()
    data._token = page.props.csrf_token

    // eslint-disable-next-line no-useless-catch
    try {
        if (method === 'PATCH') {
            method = 'POST'
            data._method = 'PATCH'
        }

        if (method === 'PUT') {
            method = 'POST'
            data._method = 'PUT'
        }

        url = url.toString()

        const response = await fetch(url, {
            method,
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: method !== 'GET' ? JSON.stringify(data) : null
        })

        if (!response.ok) {
            const errorResponse = await response.json()

            if (errorResponse?.message) {
                throw new Error('Error: ' + errorResponse.message)
            }

            throw new Error(`HTTP error! Status: ${response.status}`)
        }

        const contentType = response.headers.get('content-type')

        if (contentType?.includes('application/json')) {
            return (await response.json()) as T
        } else if (contentType?.includes('text/html')) {
            return (await response.text()) as T
        } else {
            return (await response.blob()) as T
        }
        // eslint-disable-next-line sonarjs/no-useless-catch
    } catch (error) {
        throw error
    }
}
