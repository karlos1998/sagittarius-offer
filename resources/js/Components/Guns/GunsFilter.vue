<template>
    <div class="mb-8">
        <div class="rounded border border-black/30 bg-white p-4">
            <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                <div class="flex items-center text-black/80">
                    <svg class="mr-2 h-5 w-5 text-black/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <span class="text-sm font-medium">Filtruj broń:</span>
                </div>

                <div class="flex flex-wrap gap-2">
                    <button
                        @click="selectedType = null"
                        :class="[
                            'rounded border px-3 py-1.5 text-sm font-medium transition-colors',
                            !selectedType
                                ? 'border-black bg-black text-white'
                                : 'border-black/40 bg-white text-black hover:bg-black hover:text-white'
                        ]"
                    >
                        Wszystkie
                    </button>

                    <button
                        v-for="type in gunTypes"
                        :key="type.id"
                        @click="selectedType = type.id"
                        :class="[
                            'rounded border px-3 py-1.5 text-sm font-medium transition-colors',
                            selectedType === type.id
                                ? 'border-black bg-black text-white'
                                : 'border-black/40 bg-white text-black hover:bg-black hover:text-white'
                        ]"
                    >
                        {{ type.name }}
                    </button>
                </div>
            </div>

            <div v-if="selectedType" class="mt-4 border-t border-black/20 pt-4">
                <div class="flex items-center text-sm text-black/70">
                    <span>Aktywny filtr:</span>
                    <span class="ml-2 rounded border border-black/30 px-2 py-1 text-xs font-medium text-black">
                        {{ getSelectedTypeName() }}
                    </span>
                    <button
                        @click="selectedType = null"
                        class="ml-2 text-black/60 transition-colors hover:text-black"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { GunType } from '@/types/storefront';

const props = withDefaults(
    defineProps<{
        gunTypes?: GunType[];
        modelValue?: number | null;
    }>(),
    {
        gunTypes: () => [],
        modelValue: null,
    }
);

const emit = defineEmits<{
    (event: 'update:modelValue', value: number | null): void;
}>();

const selectedType = computed({
    get: () => props.modelValue,
    set: (value: number | null) => emit('update:modelValue', value),
});

function getSelectedTypeName(): string {
    const type = props.gunTypes.find((item) => item.id === props.modelValue);
    return type ? type.name : '';
}
</script>
