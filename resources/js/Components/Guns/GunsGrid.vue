<template>
    <div class="relative py-8">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div v-if="gunPackages.length > 0" class="mb-8 rounded border border-black/30 bg-white p-4">
                <div class="mb-4 flex items-center gap-2">
                    <span class="inline-block h-2 w-2 rounded-full bg-amber-300" />
                    <h2 class="text-lg font-semibold">Gotowe pakiety</h2>
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <article
                        v-for="gunPackage in gunPackages"
                        :key="gunPackage.id"
                        class="overflow-hidden rounded border border-black/20"
                    >
                        <div class="aspect-video border-b border-black/20 bg-black/[0.02]">
                            <img
                                v-if="gunPackage.preview_image_url || gunPackage.preview_image"
                                :src="gunPackage.preview_image_url || gunPackage.preview_image"
                                :alt="gunPackage.name"
                                class="h-full w-full object-cover"
                            >
                            <div v-else class="flex h-full w-full items-center justify-center text-black/40">
                                Brak zdjęcia pakietu
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="text-base font-semibold">{{ gunPackage.name }}</h3>
                                    <p v-if="gunPackage.description" class="mt-1 text-sm text-black/60">{{ gunPackage.description }}</p>
                                </div>
                                <button
                                    class="shrink-0 rounded border border-black bg-black px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-white hover:text-black"
                                    @click="handleAddPackageToCart(gunPackage.id)"
                                >
                                    Dodaj pakiet
                                </button>
                            </div>

                            <p class="mb-2 mt-3 text-xs font-medium uppercase tracking-wide text-black/50">Pozycje pakietu</p>
                            <ul class="space-y-1 text-sm">
                                <li v-for="packageGun in gunPackage.package_guns" :key="packageGun.id" class="flex items-center gap-2">
                                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-black/70" />
                                    <span>{{ packageGun.gun?.name ?? 'Broń usunięta' }}</span>
                                    <span class="text-black/50">
                                        -
                                        {{ packageGun.shots_quantity }} strzałów
                                        ({{ packageGun.ammunition?.name ?? 'Amunicja niedostępna' }})
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </article>
                </div>
            </div>

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

        <div
            v-if="hasCartItems"
            class="fixed bottom-4 left-1/2 z-40 w-[calc(100%-1.5rem)] max-w-4xl -translate-x-1/2 rounded-xl border border-black bg-white p-3 shadow-[0_10px_30px_rgba(0,0,0,0.18)] sm:bottom-6 sm:p-4"
        >
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-black/50">Masz już coś w koszyku</p>
                    <p class="mt-1 text-base font-semibold text-black sm:text-lg">
                        {{ formattedCartAmount }}
                    </p>
                    <p class="text-sm text-black/60">
                        {{ cartItemsCount }} {{ cartItemsCount === 1 ? 'pozycja' : 'pozycji' }},
                        {{ totalShots }} {{ totalShots === 1 ? 'strzał' : 'strzałów' }}
                    </p>
                </div>

                <Link
                    :href="route('cart.index')"
                    class="inline-flex items-center justify-center rounded border border-black bg-black px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-white hover:text-black"
                >
                    Przejdź do koszyka
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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
    gunPackages: {
        type: Array,
        default: () => []
    },
    cart: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['add-to-cart', 'add-package-to-cart']);

const selectedType = ref(null);

const filteredGuns = computed(() => {
    if (!selectedType.value) {
        return props.guns;
    }
    return props.guns.filter((gun) => gun.gun_type_id === selectedType.value);
});

function handleAddToCart(gunId) {
    emit('add-to-cart', gunId);
}

function handleAddPackageToCart(packageId) {
    emit('add-package-to-cart', packageId);
}

const cartEntries = computed(() => Object.values(props.cart ?? {}));

const hasCartItems = computed(() => cartEntries.value.length > 0);

const cartItemsCount = computed(() => cartEntries.value.length);

const totalShots = computed(() =>
    cartEntries.value.reduce((sum, item) => {
        const ammunitions = item?.ammunitions ?? {};
        return sum + Object.values(ammunitions).reduce((ammoSum, quantity) => ammoSum + Number(quantity), 0);
    }, 0)
);

const cartAmount = computed(() => {
    return cartEntries.value.reduce((sum, item) => {
        const gun = props.guns.find((candidate) => candidate.id === item?.gun_id);
        const ammunitions = item?.ammunitions ?? {};

        const itemTotal = Object.entries(ammunitions).reduce((ammoTotal, [ammoId, quantity]) => {
            const ammunition = gun?.caliber?.ammunitions?.find((candidate) => candidate.id === Number(ammoId));
            const price = Number(ammunition?.standard_price ?? 0);

            return ammoTotal + price * Number(quantity);
        }, 0);

        return sum + itemTotal;
    }, 0);
});

const formattedCartAmount = computed(() =>
    new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
        minimumFractionDigits: 2,
    }).format(cartAmount.value)
);
</script>
