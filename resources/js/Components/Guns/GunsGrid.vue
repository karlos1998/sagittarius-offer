<template>
    <div class="py-8">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <GunsFilter
                :gun-types="gunTypes"
                :model-value="selectedType"
                @update:model-value="selectedType = $event"
            />

            <div v-if="filteredGuns.length === 0" class="rounded border border-black/20 px-6 py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded border border-black/30">
                    <svg class="h-8 w-8 text-black/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <h3 class="mb-2 text-xl font-semibold">
                    {{ selectedType ? 'Brak broni dla wybranego filtra' : 'Brak dostępnej broni' }}
                </h3>
                <p class="text-sm text-black/60">
                    {{
                        selectedType ? 'Spróbuj zmienić filtr lub wybierz "Wszystkie"' : 'Aktualnie nie mamy dostępnej broni w ofercie.'
                    }}
                </p>
                <button
                    v-if="selectedType"
                    @click="selectedType = null"
                    class="mt-4 rounded border border-black bg-black px-4 py-2 text-sm font-medium text-white hover:bg-white hover:text-black"
                >
                    Pokaż wszystkie bronie
                </button>
            </div>

            <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
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
