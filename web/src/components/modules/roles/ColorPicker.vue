<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-300">
            {{ label }} <span v-if="required && !disabled" class="text-red-500">*</span>
        </label>
        
        <div class="mt-1 flex space-x-2">
            <div 
                class="w-8 h-8 rounded-md border border-gray-600"
                :style="{ backgroundColor: modelValue }"
            ></div>
            
            <div class="relative flex-1">
                <input
                    type="text"
                    :id="id"
                    :value="modelValue"
                    @input="updateColor"
                    :required="required && !disabled"
                    :disabled="disabled"
                    class="w-full block bg-gray-700 border border-gray-600 rounded-lg py-2 px-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-all duration-200"
                    :class="{'border-red-500': error, 'opacity-75': disabled, 'cursor-not-allowed': disabled}"
                    placeholder="#RRGGBB"
                />
                
                <div v-if="disabled" class="absolute inset-0 cursor-not-allowed"></div>
            </div>
        </div>
        
        <p v-if="error" class="mt-1 text-sm text-red-400">{{ error }}</p>
        
        <div v-if="!disabled" class="mt-3 flex flex-wrap gap-2">
            <button 
                v-for="color in presetColors" 
                :key="color" 
                type="button"
                @click="selectColor(color)"
                class="w-6 h-6 rounded-md border border-gray-600 transition-transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900 focus:ring-violet-500"
                :style="{ backgroundColor: color }"
                :title="color"
            ></button>
        </div>
    </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    name: 'ColorPicker',
    
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
            type: String,
            required: true
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
        }
    },
    
    emits: ['update:modelValue'],
    
    setup(_, { emit }) {
        const presetColors = [
            '#9333EA', // Violet
            '#6366F1', // Indigo
            '#4F46E5', // Indigo foncé
            '#3B82F6', // Bleu
            '#0EA5E9', // Bleu ciel
            '#10B981', // Vert émeraude
            '#84CC16', // Vert lime
            '#EAB308', // Jaune
            '#F59E0B', // Ambre
            '#F97316', // Orange
            '#EF4444', // Rouge
            '#EC4899', // Rose
            '#8B5CF6', // Violet
            '#8B5CF6', // Violet
            '#6B7280', // Gris
        ];
        
        const updateColor = (event: Event) => {
            const input = event.target as HTMLInputElement;
            emit('update:modelValue', input.value);
        };
        
        const selectColor = (color: string) => {
            emit('update:modelValue', color);
        };
        
        return {
            presetColors,
            updateColor,
            selectColor
        };
    }
});
</script>