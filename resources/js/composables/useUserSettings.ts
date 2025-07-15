import { useRequest } from '@/composables/useRequest'
import { useRoute } from '@/composables/useRoute'
import { usePage } from '@inertiajs/vue3'

export function useUserSettings () {
    const props = usePage().props

    function updateSingleSetting (settingName: string, value: any) {
        return useRequest(useRoute('api.user.settings.single.update'), 'PATCH', {
            setting: settingName,
            value
        })
    }

    function updateMultipleSettings (settings: Record<string, any>) {
        return useRequest(useRoute('api.user.settings.multiple.update'), 'PATCH', {
            settings
        })
    }

    function getSingleSetting (settingName: string, defaultValue: any): any {
        const settings = props.auth?.user?.settings || {}
        const settingValue = settingName.split('.').reduce((obj, part) => obj?.[part], settings)

        if (settingValue === undefined) {
            return defaultValue
        }

        return settingValue
    }

    return {
        updateSingleSetting,
        getSingleSetting,
        updateMultipleSettings
    }
}
