<template>
    <div class="mb-8">
        <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border-2 border-red-600/30 shadow-2xl">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex items-center text-green-100">
                    <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span class="font-medium">Filtruj broń:</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        @click="selectedType = null"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-all duration-200 border-2',
                            !selectedType
                                ? 'bg-red-600 text-white border-red-500 shadow-lg'
                                : 'bg-gray-700 text-green-100 border-red-600/30 hover:bg-gray-600 hover:text-white hover:border-red-500/50'
                        ]"
                    >
                        Wszystkie
                    </button>

                    <button
                        v-for="type in gunTypes"
                        :key="type.id"
                        @click="selectedType = type.id"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-all duration-200 border-2',
                            selectedType === type.id
                                ? 'bg-red-600 text-white border-red-500 shadow-lg'
                                : 'bg-gray-700 text-green-100 border-red-600/30 hover:bg-gray-600 hover:text-white hover:border-red-500/50'
                        ]"
                    >
                        {{ type.name }}
                    </button>
                </div>
            </div>

            <div v-if="selectedType" class="mt-4 pt-4 border-t border-red-600/30">
                <div class="flex items-center text-sm text-green-200">
                    <span>Aktywny filtr:</span>
                    <span
                        class="ml-2 px-2 py-1 bg-red-600/20 text-red-400 rounded text-xs font-medium border border-red-500/30">
                        {{ getSelectedTypeName() }}
                    </span>
                    <button
                        @click="selectedType = null"
                        class="ml-2 text-green-300 hover:text-red-400 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';

const props = defineProps({
    gunTypes: {
        type: Array,
        default: () => []
    },
    modelValue: {
        type: [Number, null],
        default: null
    }
});

const emit = defineEmits(['update:modelValue']);

const selectedType = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

function getSelectedTypeName() {
    const type = props.gunTypes.find(t => t.id === props.modelValue);
    return type ? type.name : '';
}
</script>
