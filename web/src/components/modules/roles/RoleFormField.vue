<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-300">
            {{ label }} <span v-if="required && !disabled" class="text-red-500">*</span>
        </label>
        
        <div class="relative mt-1">
            <slot>
                <input
                    :type="type"
                    :id="id"
                    :value="modelValue"
                    @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                    :required="required && !disabled"
                    :disabled="disabled"
                    class="block w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                    :class="{'border-red-500': error, 'opacity-75': disabled, 'cursor-not-allowed': disabled}"
                    :placeholder="placeholder"
                />
            </slot>
            
            <div v-if="disabled" class="absolute inset-0 cursor-not-allowed"></div>
        </div>
        
        <p v-if="error" class="mt-1 text-sm text-red-400">{{ error }}</p>
        <p v-else-if="helpText" class="mt-1 text-xs text-gray-400">{{ helpText }}</p>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'RoleFormField',
    
    props: {
        id: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: true
        },
        modelValue: {
            type: [String, Number],
            default: ''
        },
        type: {
            type: String,
            default: 'text'
        },
        required: {
            type: Boolean,
            default: false
        },
        disabled: {
            type: Boolean,
            default: false
        },
        error: {
            type: String,
            default: ''
        },
        placeholder: {
            type: String,
            default: ''
        },
        helpText: {
            type: String,
            default: ''
        }
    },
    
    emits: ['update:modelValue']
});
</script>