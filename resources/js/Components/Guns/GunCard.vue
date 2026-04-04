<template>
    <div
        class="overflow-hidden rounded border border-black/30 bg-white transition-colors hover:border-black">
        <!-- Gun Image -->
        <div class="relative aspect-video overflow-hidden border-b border-black/20 bg-white">
            <div v-if="gun.photos && gun.photos.length > 0" class="w-full h-full">
                <img
                    :src="gun.photo_urls?.[0] || gun.photos[0]"
                    :alt="gun.name"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                />
            </div>
            <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="h-12 w-12 text-black/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <!-- Gun Info -->
        <div class="p-6">
            <h3 class="mb-2 text-xl font-semibold">{{ gun.name }}</h3>

            <div class="mb-4 space-y-2">
                <div class="flex items-center text-black/70">
                    <svg class="mr-2 h-4 w-4 text-black/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-9 0V1m10 3V1m0 3l1 1v16a2 2 0 01-2 2H6a2 2 0 01-2-2V8l1-1z" />
                    </svg>
                    <span class="text-sm">{{ gun.gun_type?.name || 'Nieznany typ' }}</span>
                </div>

                <div class="flex items-center text-black/70">
                    <svg class="mr-2 h-4 w-4 text-black/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm">Kaliber: {{ gun.caliber?.name || 'Nieznany' }}</span>
                </div>
            </div>

            <!-- Ammunition Section -->
            <div v-if="gun.caliber?.ammunitions && gun.caliber.ammunitions.length > 0" class="mb-4">
                <h4 class="mb-2 flex items-center text-sm font-medium text-black/70">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                    </svg>
                    Dostępna amunicja
                </h4>
                <div class="space-y-1">
                    <div
                        v-for="ammo in gun.caliber.ammunitions"
                        :key="ammo.id"
                        class="flex items-center justify-between rounded border border-black/20 px-2 py-1 text-sm text-black/80"
                    >
                        <span class="font-medium">{{ ammo.name }}</span>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="font-semibold">{{ ammo.club_price }} zł</span>
                            <span class="text-black/50">(klub)</span>
                            <span class="text-black/30">/</span>
                            <span>{{ ammo.standard_price }} zł</span>
                            <span class="text-black/50">(standard)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="gun.description" class="text-sm text-black/70">
                {{ gun.description }}
            </div>
        </div>

        <!-- Add to Cart Button -->
        <div class="px-6 pb-6">
            <button
                @click="addToCart"
                :disabled="isInCart"
                class="w-full rounded border border-black bg-black px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white hover:text-black disabled:cursor-not-allowed disabled:border-black/30 disabled:bg-black/20 disabled:text-black/50"
            >
                <span v-if="isInCart" class="flex items-center justify-center">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    W koszyku
                </span>
                <span v-else class="flex items-center justify-center">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2 8h12l-2-8M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-3" />
                    </svg>
                    Dodaj do koszyka (5 strzałów)
                </span>
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { CartMap, Gun } from '@/types/storefront';

const props = withDefaults(
    defineProps<{
        gun: Gun;
        cart?: CartMap;
    }>(),
    {
        cart: () => ({}),
    }
);

const emit = defineEmits(['add-to-cart']);

const isInCart = computed(() => {
    return props.cart && props.cart[String(props.gun.id)] !== undefined;
});

function handleImageError(event: Event): void {
    const target = event.target as HTMLImageElement | null;

    if (!target) {
        return;
    }

    target.style.display = 'none';
}

function addToCart(): void {
    if (!isInCart.value) {
        emit('add-to-cart', props.gun.id);
    }
}
</script>
