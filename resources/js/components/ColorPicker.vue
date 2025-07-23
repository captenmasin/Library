<script setup lang="ts">
import '~/css/color-picker.css'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { ref, computed, defineProps, defineEmits } from 'vue'
import { Vue3ColorPicker } from '@cyhnkckali/vue3-color-picker'

import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover'

const props = defineProps<{
    modelValue: string;
    name?: string;
    class?: string;
    disabled?: boolean;
}>()

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'blur'): void;
}>()

const open = ref(false)

const proxyModelValue = computed({
    get: () => props.modelValue,
    set: (value) => {
        console.log(value)
        emit('update:modelValue', value)
    }
})

const parsedValue = computed(() => props.modelValue || '#FFFFFF')

function onBlur () {
    emit('blur')
}
</script>

<template>
    <Popover v-model:open="open">
        <PopoverTrigger as-child>
            <Button
                :name="props.name"
                :disabled="props.disabled"
                :class="cn('block', props.class)"
                variant="outline"
                size="icon"
                :style="{ backgroundColor: parsedValue }"
                @click="open = true"
                @blur="onBlur"
            >
                <div class="h-5 w-5" />
            </Button>
        </PopoverTrigger>

        <PopoverContent class="w-full space-y-2">
            <Vue3ColorPicker
                v-model="proxyModelValue"
                mode="solid"
                :show-alpha="false"
                :show-buttons="false"
                :show-picker-mode="false"
                :show-input-menu="false"
                :show-color-list="false"
                :show-eye-drop="false"
                type="HEX"
            />

            <!--            <Input-->
            <!--                v-model="parsedValue"-->
            <!--                :maxlength="7"-->
            <!--                @input="e => updateColor((e.target as HTMLInputElement).value)"-->
            <!--            />-->
        </PopoverContent>
    </Popover>
</template>
