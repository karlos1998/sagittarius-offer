<template>
    <div
        class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl overflow-hidden border-2 border-red-600/30 hover:border-red-500/60 transition-all duration-300 hover:transform hover:scale-105 shadow-2xl">
        <!-- Gun Image -->
        <div class="aspect-video bg-gray-900 relative overflow-hidden border-b border-red-600/30">
            <div v-if="gun.photos && gun.photos.length > 0" class="w-full h-full">
                <img
                    :src="gun.photos[0]"
                    :alt="gun.name"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                />
            </div>
            <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <!-- Gun Info -->
        <div class="p-6">
            <h3 class="text-xl font-bold text-white mb-2">{{ gun.name }}</h3>

            <div class="space-y-2 mb-4">
                <div class="flex items-center text-green-100">
                    <svg class="w-4 h-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-9 0V1m10 3V1m0 3l1 1v16a2 2 0 01-2 2H6a2 2 0 01-2-2V8l1-1z"/>
                    </svg>
                    <span class="text-sm">{{ gun.gun_type?.name || 'Nieznany typ' }}</span>
                </div>

                <div class="flex items-center text-green-100">
                    <svg class="w-4 h-4 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm">Kaliber: {{ gun.caliber?.name || 'Nieznany' }}</span>
                </div>
            </div>

            <!-- Ammunition Section -->
            <div v-if="gun.caliber?.ammunitions && gun.caliber.ammunitions.length > 0" class="mb-4">
                <h4 class="text-sm font-medium text-red-400 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                    Dostępna amunicja
                </h4>
                <div class="space-y-1">
                    <div
                        v-for="ammo in gun.caliber.ammunitions"
                        :key="ammo.id"
                        class="flex items-center justify-between text-sm text-green-100 bg-gray-700/50 rounded px-2 py-1 border border-red-600/20"
                    >
                        <span class="font-medium">{{ ammo.name }}</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-red-400 font-semibold">{{ ammo.club_price }}zł</span>
                            <span class="text-green-300 text-xs">(klub)</span>
                            <span class="text-red-300">/</span>
                            <span class="text-green-300">{{ ammo.standard_price }}zł</span>
                            <span class="text-green-300 text-xs">(standard)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="gun.description" class="text-green-200 text-sm">
                {{ gun.description }}
            </div>
        </div>

        <!-- Add to Cart Button -->
        <div class="px-6 pb-6">
            <button
                @click="addToCart"
                :disabled="isInCart"
                class="w-full bg-red-600 hover:bg-red-700 disabled:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 border-2 border-red-500 disabled:border-gray-500"
            >
                <span v-if="isInCart" class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    W koszyku
                </span>
                <span v-else class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2 8h12l-2-8M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-3"/>
                    </svg>
                    Dodaj do koszyka (5 strzałów)
                </span>
            </button>
        </div>
    </div>
</template>

<script setup>
import {computed} from "vue";

const props = defineProps({
    gun: {
        type: Object,
        required: true
    },
    cart: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['add-to-cart']);

const isInCart = computed(() => {
    return props.cart && props.cart[props.gun.id] !== undefined;
});

function handleImageError(event) {
    event.target.style.display = 'none';
    const parent = event.target.parentElement;
    const fallback = parent.querySelector('.fallback-icon') || document.createElement('div');
    if (!fallback.classList.contains('fallback-icon')) {
        fallback.className = 'fallback-icon w-full h-full flex items-center justify-center';
        fallback.innerHTML = `
            <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        `;
        parent.appendChild(fallback);
    }
    fallback.style.display = 'flex';
}

function addToCart() {
    if (!isInCart.value) {
        emit('add-to-cart', props.gun.id);
    }
}
</script>
