<template>
    <div class="min-h-screen bg-white text-black">
        <SimpleNavbar :cart="cart" />

        <div class="border-b border-black/20 bg-white py-8">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-4 px-4 sm:px-6 lg:px-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <Link :href="route('guns.index')" class="inline-flex items-center text-sm text-black/70 hover:text-black">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Powrót do oferty
                    </Link>
                    <h1 class="mt-2 inline-flex items-center gap-2 text-3xl font-semibold">
                        <span class="inline-block h-2 w-2 rounded-full bg-amber-300" />
                        Koszyk zamówień
                    </h1>
                    <p class="mt-1 text-sm text-black/60">Proste podsumowanie Twojego zamówienia.</p>
                </div>

                <div class="rounded border border-black px-4 py-3 text-sm">
                    <div>Razem strzałów: <strong>{{ totalShots }}</strong></div>
                    <div>Do zapłaty: <strong>{{ formatPrice(totalPrice) }} zł</strong></div>
                </div>
            </div>
        </div>

        <div class="py-8">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-6 rounded border border-black/30 bg-white p-4">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <h3 class="font-medium">Członek Klubu Sagittarius</h3>
                            <p class="text-sm text-black/60">Przełącz cenę klubową dla amunicji.</p>
                        </div>
                        <label class="relative inline-flex cursor-pointer items-center">
                            <input
                                type="checkbox"
                                :checked="isClubMember"
                                @change="toggleClubMember"
                                class="peer sr-only"
                            >
                            <div class="h-6 w-11 rounded-full border border-black bg-white peer-checked:bg-black peer-checked:after:translate-x-full after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-black after:bg-white after:transition-all"></div>
                            <span class="ml-3 text-xs font-semibold tracking-wide">{{ isClubMember ? 'TAK' : 'NIE' }}</span>
                        </label>
                    </div>
                </div>

                <div v-if="cartItems.length === 0" class="rounded border border-black/30 bg-white px-6 py-12 text-center">
                    <h3 class="text-xl font-semibold">Koszyk jest pusty</h3>
                    <p class="mt-2 text-sm text-black/60">Dodaj broń, aby rozpocząć zamówienie.</p>
                    <Link
                        :href="route('guns.index')"
                        class="mt-6 inline-flex items-center rounded border border-black bg-black px-5 py-2 text-sm font-medium text-white hover:bg-white hover:text-black"
                    >
                        Przejdź do oferty
                    </Link>
                </div>

                <div v-else class="space-y-5">
                    <CartItem
                        v-for="item in cartItems"
                        :key="item.gun.id"
                        :item="item"
                        :is-club-member="isClubMember"
                        @update-quantity="updateQuantity"
                        @add-ammunition="addAmmunition"
                        @remove-ammunition="requestRemoveAmmunition"
                        @remove="requestRemoveItem"
                    />

                    <div class="rounded border border-black bg-white p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">Podsumowanie</h3>
                                <p class="text-sm text-black/60">Razem: {{ totalShots }} strzałów</p>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-2xl font-semibold">{{ formatPrice(totalPrice) }} zł</div>
                                <p class="text-xs text-black/60">{{ isClubMember ? 'Cena klubowa' : 'Cena standardowa' }}</p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                            <button
                                @click="requestClearCart"
                                class="rounded border border-black px-5 py-2 text-sm font-medium hover:bg-black hover:text-white"
                            >
                                Wyczyść koszyk
                            </button>
                            <Link
                                :href="route('checkout.index')"
                                class="rounded border border-black bg-black px-5 py-2 text-center text-sm font-medium text-white hover:bg-white hover:text-black"
                            >
                                Złóż zamówienie
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmationModal :show="confirmation.show" @close="closeConfirmation">
            <template #title>
                {{ confirmation.title }}
            </template>

            <template #content>
                {{ confirmation.message }}
            </template>

            <template #footer>
                <div class="flex flex-wrap justify-end gap-2">
                    <button
                        type="button"
                        class="rounded border border-black px-4 py-2 text-sm font-medium text-black hover:bg-black hover:text-white"
                        @click="closeConfirmation"
                    >
                        Anuluj
                    </button>
                    <button
                        type="button"
                        class="rounded border border-black bg-black px-4 py-2 text-sm font-medium text-white hover:bg-white hover:text-black"
                        @click="confirmAction"
                    >
                        Usuń
                    </button>
                </div>
            </template>
        </ConfirmationModal>

        <Footer />
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ConfirmationModal from '../../Components/ConfirmationModal.vue';
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import CartItem from '../../Components/Cart/CartItem.vue';
import type { CartMap, Gun } from '@/types/storefront';
import { buildCartDisplayItems, calculateTotalPrice, calculateTotalShots } from '@/utils/cart';
import { formatPrice } from '@/utils/format';

type CartUpdateAction = 'increase' | 'decrease';
type ConfirmationAction = 'remove-ammunition' | 'remove-item' | 'clear-cart' | null;

interface ConfirmationState {
    show: boolean;
    title: string;
    message: string;
    action: ConfirmationAction;
    payload: {
        gunId?: number;
        ammoId?: number;
    };
}

const props = withDefaults(
    defineProps<{
        cart?: CartMap;
        guns?: Gun[];
        isClubMember?: boolean;
    }>(),
    {
        cart: () => ({}),
        guns: () => [],
        isClubMember: false,
    }
);

const isClubMember = ref(props.isClubMember);
const confirmation = ref<ConfirmationState>({
    show: false,
    title: '',
    message: '',
    action: null,
    payload: {},
});

const cartItems = computed(() => buildCartDisplayItems(props.cart, props.guns, isClubMember.value));

const totalShots = computed(() => calculateTotalShots(cartItems.value));
const totalPrice = computed(() => calculateTotalPrice(cartItems.value));

function updateQuantity(gunId: number, ammoId: number, action: CartUpdateAction): void {
    router.post(
        route('cart.update'),
        {
            gun_id: gunId,
            action,
            ammo_id: ammoId,
        },
        {
            preserveScroll: true,
        }
    );
}

function addAmmunition(gunId: number, ammoId: number, quantity?: number): void {
    router.post(
        route('cart.update'),
        {
            gun_id: gunId,
            action: 'add_ammo',
            ammo_id: ammoId,
            quantity,
        },
        {
            preserveScroll: true,
        }
    );
}

function removeAmmunition(gunId: number, ammoId: number): void {
    router.post(
        route('cart.update'),
        {
            gun_id: gunId,
            action: 'remove_ammo',
            ammo_id: ammoId,
        },
        {
            preserveScroll: true,
        }
    );
}

function removeItem(gunId: number): void {
    router.post(
        route('cart.update'),
        {
            gun_id: gunId,
            action: 'remove',
        },
        {
            preserveScroll: true,
        }
    );
}

function requestRemoveAmmunition(gunId: number, ammoId: number, ammoName: string): void {
    openConfirmation(
        'remove-ammunition',
        {
            gunId,
            ammoId,
        },
        'Usuwanie amunicji',
        `Czy na pewno chcesz usunąć amunicję "${ammoName}" z koszyka?`
    );
}

function requestRemoveItem(gunId: number, gunName: string): void {
    openConfirmation(
        'remove-item',
        {
            gunId,
        },
        'Usuwanie broni',
        `Czy na pewno chcesz usunąć broń "${gunName}" z koszyka?`
    );
}

function toggleClubMember(): void {
    isClubMember.value = !isClubMember.value;
    router.post(
        route('cart.toggle-club-member'),
        {
            is_club_member: isClubMember.value,
        },
        {
            preserveScroll: true,
        }
    );
}

function requestClearCart(): void {
    openConfirmation(
        'clear-cart',
        {},
        'Wyczyszczenie koszyka',
        'Czy na pewno chcesz usunąć wszystkie pozycje z koszyka?'
    );
}

function openConfirmation(
    action: ConfirmationAction,
    payload: ConfirmationState['payload'],
    title: string,
    message: string
): void {
    confirmation.value = {
        show: true,
        title,
        message,
        action,
        payload,
    };
}

function closeConfirmation(): void {
    confirmation.value.show = false;
}

function confirmAction(): void {
    const { action, payload } = confirmation.value;

    closeConfirmation();

    if (action === 'remove-ammunition') {
        if (payload.gunId !== undefined && payload.ammoId !== undefined) {
            removeAmmunition(payload.gunId, payload.ammoId);
        }
        return;
    }

    if (action === 'remove-item') {
        if (payload.gunId !== undefined) {
            removeItem(payload.gunId);
        }
        return;
    }

    if (action === 'clear-cart') {
        router.post(
            route('cart.clear'),
            {},
            {
                preserveScroll: true,
            }
        );
    }
}
</script>
