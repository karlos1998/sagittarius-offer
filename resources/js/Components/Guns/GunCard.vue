<template>
    <article class="flex flex-col overflow-hidden rounded-3xl border border-black/10 bg-white transition-shadow hover:shadow-[0_12px_30px_rgba(0,0,0,0.08)]">
        <ExpandableGunPhoto :src="previewPhoto" :alt="gun.name" />

        <div class="flex flex-1 flex-col p-6">
            <div class="flex flex-wrap gap-2 text-xs font-medium">
                <span class="rounded-full bg-stone-100 px-2.5 py-1 text-black/70">{{ gun.gun_type?.name || 'Broń' }}</span>
                <span v-if="gun.caliber?.name" class="rounded-full bg-stone-100 px-2.5 py-1 text-black/70">{{ gun.caliber.name }}</span>
            </div>

            <h3 class="mt-3 text-xl font-semibold text-black">{{ gun.name }}</h3>

            <p
                v-if="gun.description"
                class="mt-2 text-sm leading-6 text-black/60"
                :class="{ 'line-clamp-3': !isDescriptionExpanded }"
            >
                {{ gun.description }}
            </p>
            <button
                v-if="gun.description && gun.description.length > 140"
                type="button"
                class="mt-1 w-fit text-xs font-semibold text-black/60 underline underline-offset-2 hover:text-black"
                @click="isDescriptionExpanded = !isDescriptionExpanded"
            >
                {{ isDescriptionExpanded ? 'Zwiń' : 'Więcej' }}
            </button>

            <ul v-if="ammunitions.length" class="mt-5 flex flex-col gap-2">
                <li
                    v-for="ammo in ammunitions"
                    :key="ammo.id"
                    class="flex items-center justify-between gap-3 rounded-2xl bg-stone-50 px-4 py-3"
                >
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-black">{{ ammo.name }}</p>
                        <p class="text-xs text-black/50">
                            {{ formatPerShot(ammo.selectedPrice) }} za strzał
                            <template v-if="!isClubMember"> · klubowicze {{ formatPerShot(ammo.clubPrice) }}</template>
                        </p>
                    </div>
                    <p class="shrink-0 text-right">
                        <span class="block text-lg font-semibold leading-tight text-black">{{ formatTotal(ammo.selectedPrice * ammo.step) }}</span>
                        <span class="text-xs text-black/50">za {{ ammo.step }} {{ shotsWord(ammo.step) }}</span>
                    </p>
                </li>
            </ul>

            <div class="mt-auto pt-6">
                <button
                    type="button"
                    :disabled="isInCart"
                    class="flex w-full items-center justify-center gap-2 rounded-full border border-black bg-black px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-black disabled:cursor-default disabled:border-emerald-300 disabled:bg-emerald-50 disabled:text-emerald-800"
                    @click="addToCart"
                >
                    <svg v-if="isInCart" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ isInCart ? 'W koszyku' : addToCartLabel }}
                </button>
            </div>
        </div>
    </article>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import type { CartMap, Gun } from '@/types/storefront';
import { toNumber } from '@/utils/format';
import { pluralPl } from '@/utils/schedule';
import ExpandableGunPhoto from './ExpandableGunPhoto.vue';

const props = withDefaults(
    defineProps<{
        gun: Gun;
        cart?: CartMap;
        isClubMember?: boolean;
    }>(),
    {
        cart: () => ({}),
        isClubMember: false,
    }
);

const emit = defineEmits(['add-to-cart']);

const isDescriptionExpanded = ref(false);

const isInCart = computed(() => props.cart?.[String(props.gun.id)] !== undefined);

const ammunitions = computed(() =>
    (props.gun.caliber?.ammunitions ?? []).map((ammo) => {
        const clubPrice = toNumber(ammo.club_price);
        const standardPrice = toNumber(ammo.standard_price);

        return {
            id: ammo.id,
            name: ammo.name,
            step: Math.max(Number(ammo.cart_quantity_step ?? 10), 1),
            clubPrice,
            selectedPrice: props.isClubMember ? clubPrice : standardPrice,
        };
    })
);

const addToCartLabel = computed(() => {
    const step = ammunitions.value[0]?.step ?? 10;

    return `Dodaj ${step} ${shotsWord(step)} do koszyka`;
});

const previewPhoto = computed(() => props.gun.photo_urls?.[0] || props.gun.photos?.[0] || null);

function shotsWord(count: number): string {
    return pluralPl(count, 'strzał', 'strzały', 'strzałów');
}

function formatPerShot(value: number): string {
    return new Intl.NumberFormat('pl-PL', { style: 'currency', currency: 'PLN', minimumFractionDigits: 2 }).format(value);
}

function formatTotal(value: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2,
    }).format(value);
}

function addToCart(): void {
    if (!isInCart.value) {
        emit('add-to-cart', props.gun.id);
    }
}
</script>
