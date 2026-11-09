<template>
    <div class="relative pb-28 pt-10">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <section v-if="packages.length > 0" class="mb-14">
                <div class="max-w-2xl">
                    <h2 class="text-2xl font-semibold tracking-tight text-black sm:text-3xl">Gotowe zestawy</h2>
                    <p class="mt-2 text-black/60">Nie wiesz, co wybrać? Weź kilka rodzajów broni naraz i porównaj.</p>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="gunPackage in packages"
                        :key="gunPackage.id"
                        class="flex flex-col overflow-hidden rounded-3xl border border-black/10 bg-stone-50"
                    >
                        <img
                            v-if="gunPackage.imageUrl"
                            :src="gunPackage.imageUrl"
                            :alt="gunPackage.name"
                            class="aspect-video w-full object-cover"
                            loading="lazy"
                        >

                        <div class="flex flex-1 flex-col p-6">
                            <h3 class="text-lg font-semibold text-black">{{ gunPackage.name }}</h3>
                            <p v-if="gunPackage.description" class="mt-1 text-sm leading-6 text-black/60">{{ gunPackage.description }}</p>

                            <ul class="mt-4 flex flex-col divide-y divide-black/10 rounded-2xl bg-white px-4 text-sm">
                                <li
                                    v-for="item in gunPackage.items"
                                    :key="item.id"
                                    class="flex items-baseline justify-between gap-3 py-2.5"
                                >
                                    <span class="min-w-0">
                                        <span class="font-medium text-black">{{ item.gunName }}</span>
                                        <span v-if="item.ammunitionName" class="block text-xs text-black/50">{{ item.ammunitionName }}</span>
                                    </span>
                                    <span class="shrink-0 text-black/70">{{ item.shots }} {{ shotsWord(item.shots) }}</span>
                                </li>
                            </ul>

                            <div class="mt-auto flex items-end justify-between gap-4 pt-6">
                                <p>
                                    <span class="block text-2xl font-semibold leading-tight text-black">{{ formatTotal(gunPackage.total) }}</span>
                                    <span class="text-xs text-black/50">{{ gunPackage.totalShots }} {{ shotsWord(gunPackage.totalShots) }}{{ isClubMember ? ', cena klubowa' : '' }}</span>
                                </p>
                                <button
                                    type="button"
                                    class="shrink-0 rounded-full border border-black bg-black px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-black"
                                    @click="handleAddPackageToCart(gunPackage.id)"
                                >
                                    Dodaj zestaw
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <section>
                <h2 class="mb-2 text-2xl font-semibold tracking-tight text-black sm:text-3xl">Cała broń</h2>

                <GunsFilter
                    :gun-types="gunTypes"
                    :guns="guns"
                    :model-value="selectedType"
                    :is-club-member="isClubMember"
                    @update:model-value="selectedType = $event"
                    @update:is-club-member="emit('update:isClubMember', $event)"
                />

                <div v-if="filteredGuns.length === 0" class="rounded-3xl border border-dashed border-black/20 px-6 py-12 text-center">
                    <h3 class="text-xl font-semibold">
                        {{ selectedType ? 'Brak broni w tej kategorii' : 'Brak dostępnej broni' }}
                    </h3>
                    <button
                        v-if="selectedType"
                        type="button"
                        class="mt-4 rounded-full border border-black bg-black px-5 py-2 text-sm font-semibold text-white hover:bg-white hover:text-black"
                        @click="selectedType = null"
                    >
                        Pokaż całą broń
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
                    <GunCard
                        v-for="gun in filteredGuns"
                        :key="gun.id"
                        :gun="gun"
                        :cart="cart"
                        :is-club-member="isClubMember"
                        @add-to-cart="handleAddToCart"
                    />
                </div>
            </section>
        </div>

        <div
            v-if="hasCartItems"
            class="fixed bottom-4 left-1/2 z-40 w-[calc(100%-1.5rem)] max-w-4xl -translate-x-1/2 rounded-2xl border border-black bg-white p-3 shadow-[0_10px_30px_rgba(0,0,0,0.18)] sm:bottom-6 sm:p-4"
        >
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-base font-semibold text-black sm:text-lg">
                        {{ formattedCartAmount }}
                    </p>
                    <p class="truncate text-sm text-black/60">
                        {{ cartItemsCount }} {{ pluralPl(cartItemsCount, 'pozycja', 'pozycje', 'pozycji') }},
                        {{ totalShots }} {{ shotsWord(totalShots) }}
                    </p>
                </div>

                <Link
                    :href="route('cart.index')"
                    class="inline-flex shrink-0 items-center justify-center rounded-full border border-black bg-black px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-black"
                >
                    Przejdź do koszyka
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { CartMap, Gun, GunPackage, GunType } from '@/types/storefront';
import { calculateCartValueFromRawData, countCartItems } from '@/utils/cart';
import { formatCurrencyPLN, toNumber } from '@/utils/format';
import { pluralPl } from '@/utils/schedule';
import GunCard from './GunCard.vue';
import GunsFilter from './GunsFilter.vue';

const props = withDefaults(
    defineProps<{
        guns?: Gun[];
        gunTypes?: GunType[];
        gunPackages?: GunPackage[];
        cart?: CartMap;
        isClubMember?: boolean;
    }>(),
    {
        guns: () => [],
        gunTypes: () => [],
        gunPackages: () => [],
        cart: () => ({}),
        isClubMember: false,
    }
);

const emit = defineEmits<{
    (event: 'add-to-cart', gunId: number): void;
    (event: 'add-package-to-cart', packageId: number): void;
    (event: 'update:isClubMember', value: boolean): void;
}>();

const selectedType = ref<number | null>(null);

const filteredGuns = computed(() => {
    if (!selectedType.value) {
        return props.guns;
    }

    return props.guns.filter((gun) => gun.gun_type_id === selectedType.value);
});

const packages = computed(() =>
    props.gunPackages.map((gunPackage) => {
        const items = (gunPackage.package_guns ?? []).map((packageGun) => ({
            id: packageGun.id,
            gunName: packageGun.gun?.name ?? 'Broń niedostępna',
            ammunitionName: packageGun.ammunition?.name ?? null,
            shots: packageGun.shots_quantity,
            pricePerShot: toNumber(props.isClubMember ? packageGun.ammunition?.club_price : packageGun.ammunition?.standard_price),
        }));

        return {
            id: gunPackage.id,
            name: gunPackage.name,
            description: gunPackage.description,
            imageUrl: gunPackage.preview_image_url || gunPackage.preview_image || null,
            items,
            totalShots: items.reduce((sum, item) => sum + item.shots, 0),
            total: items.reduce((sum, item) => sum + item.shots * item.pricePerShot, 0),
        };
    })
);

function shotsWord(count: number): string {
    return pluralPl(count, 'strzał', 'strzały', 'strzałów');
}

function formatTotal(value: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value);
}

function handleAddToCart(gunId: number): void {
    emit('add-to-cart', gunId);
}

function handleAddPackageToCart(packageId: number): void {
    emit('add-package-to-cart', packageId);
}

const cartEntries = computed(() => Object.values(props.cart ?? {}));

const hasCartItems = computed(() => cartEntries.value.length > 0);

const cartItemsCount = computed(() => countCartItems(props.cart));

const totalShots = computed(() =>
    cartEntries.value.reduce((sum, item) => {
        const ammunitions = item?.ammunitions ?? {};

        return sum + Object.values(ammunitions).reduce((ammoSum, quantity) => ammoSum + Number(quantity), 0);
    }, 0)
);

const cartAmount = computed(() => calculateCartValueFromRawData(props.cart, props.guns, props.isClubMember));

const formattedCartAmount = computed(() => formatCurrencyPLN(cartAmount.value));
</script>
