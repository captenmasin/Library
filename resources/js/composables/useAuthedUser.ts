import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { UserPermission } from '@/enums/UserPermission'

export function useAuthedUser () {
    const page = usePage()

    const auth = computed(() => page.props.auth || {})
    const authedUser = computed(() => auth.value.user || null)
    const authed = computed(() => auth.value.check || false)

    const permissions = computed(() => authedUser.value?.permissions || [])

    function hasPermission (permission: string | UserPermission): boolean {
        return permissions.value.includes(permission)
    }

    return {
        hasPermission,
        authedUser,
        authed
    }
}
