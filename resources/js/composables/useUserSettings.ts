import { useRequest } from '@/composables/useRequest'
import { useRoute } from '@/composables/useRoute'
import { usePage } from '@inertiajs/vue3'

export function useUserSettings () {
    const props = usePage().props
    console.log(props.auth.user)

    function updateSingleSettings (settingName: string, value: any) {
        useRequest(useRoute('api.user.settings.update'), 'PATCH', {
            setting: settingName,
            value
        }).then((response) => {
            if (response.status === 200) {
                console.log(`Setting ${settingName} updated successfully.`)
            } else {
                console.error(`Failed to update setting ${settingName}:`, response.data)
            }
        }).catch((error) => {
            console.error(`Error updating setting ${settingName}:`, error)
        })
    }

    return {
        updateSingleSettings
    }
}
