import { useRequest } from '@/composables/useRequest'
import { useRoute } from '@/composables/useRoute'
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue'

export function useUserSettings () {
    const props = usePage().props

    function updateSingleSettings (settingName: string, value: any) {
        useRequest(useRoute('api.user.settings.update'), 'PATCH', {
            setting: settingName,
            value
        }).then(r => {
            // All cool
        })
    }

    function getSingleSetting (settingName: string) {
        const settings = ref(props.auth.user.settings || {})
        return settingName.split('.').reduce((obj, part) => obj?.[part], settings.value)
    }

    return {
        updateSingleSettings,
        getSingleSetting
    }
}
