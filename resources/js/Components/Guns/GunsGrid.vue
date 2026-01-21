<template>
    <div class="relative py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <GunsFilter
                :gun-types="gunTypes"
                :model-value="selectedType"
                @update:model-value="selectedType = $event"
            />

            <div v-if="filteredGuns.length === 0" class="text-center py-16">
                <div
                    class="w-24 h-24 mx-auto mb-6 bg-gray-800 rounded-full flex items-center justify-center border-2 border-red-600/30">
                    <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-white mb-2">
                    {{ selectedType ? 'Brak broni dla wybranego filtra' : 'Brak dostępnej broni' }}
                </h3>
                <p class="text-green-200">
                    {{
                        selectedType ? 'Spróbuj zmienić filtr lub wybierz "Wszystkie"' : 'Aktualnie nie mamy dostępnej broni w ofercie.'
                    }}
                </p>
                <button
                    v-if="selectedType"
                    @click="selectedType = null"
                    class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium border-2 border-red-500"
                >
                    Pokaż wszystkie bronie
                </button>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <GunCard
                    v-for="gun in filteredGuns"
                    :key="gun.id"
                    :gun="gun"
                    :cart="cart"
                    @add-to-cart="handleAddToCart"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';
import GunCard from './GunCard.vue';
import GunsFilter from './GunsFilter.vue';

const props = defineProps({
    guns: {
        type: Array,
        default: () => []
    },
    gunTypes: {
        type: Array,
        default: () => []
    },
    cart: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['add-to-cart']);

const selectedType = ref(null);

const filteredGuns = computed(() => {
    if (!selectedType.value) {
        return props.guns;
    }
    return props.guns.filter(gun => gun.gun_type_id === selectedType.value);
});

function handleAddToCart(gunId) {
    emit('add-to-cart', gunId);
}
</script>
