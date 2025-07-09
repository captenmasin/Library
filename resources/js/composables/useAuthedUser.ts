import { usePage } from '@inertiajs/vue3'
import { UserPermission } from '@/enums/UserPermission'

export function useAuthedUser () {
    const page = usePage()

    const auth = page.props.auth
    const authedUser = auth?.user || null
    const authed = auth?.check || false

    function hasPermission (permission: string | UserPermission): boolean {
        return authedUser?.permissions?.includes(permission) || false
    }

    return {
        hasPermission,
        authedUser,
        authed
    }
}
