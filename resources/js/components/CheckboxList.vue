<script setup lang="ts">
import { useId } from 'reka-ui'
import { Label } from '@/components/ui/label'
import { Checkbox } from '@/components/ui/checkbox'

interface Option {
    label: string
    value: string
}

defineProps({
    options: {
        type: Array as () => Option[],
        required: true
    },
    modelValue: {
        type: Array as () => string[],
        required: true
    }
})

const proxyStatus = defineModel<string[]>('modelValue')

const idPrefix = useId()
</script>

<template>
    <ul class="flex flex-col gap-2">
        <li
            v-for="option in options"
            :key="option.value"
            class="flex items-center">
            <Checkbox
                :id="`${idPrefix}-${option.value}`"
                :checked="proxyStatus?.includes(option.value)"
                class="mr-2"
                @update:checked="checked => {
                    if (!proxyStatus) return

                    if (checked) {
                        if (!proxyStatus.includes(option.value)) {
                            proxyStatus.push(option.value)
                        }
                    } else {
                        const i = proxyStatus.indexOf(option.value)
                        if (i !== -1) proxyStatus.splice(i, 1)
                    }
                }"
            />
            <label
                :for="`${idPrefix}-${option.value}`"
                class="text-sm">
                {{ option.label }}
            </label>
        </li>
    </ul>
</template>
