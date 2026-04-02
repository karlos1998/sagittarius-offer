<template>
    <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-950">
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 to-black">
            <div class="absolute inset-0 opacity-20">
                <div class="absolute inset-0 bg-gradient-to-br from-gray-800/30 to-gray-900/20"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="flex items-center justify-between">
                    <div>
                        <Link :href="route('guns.index')"
                              class="inline-flex items-center text-gray-400 hover:text-white mb-4 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Powrót do oferty broni
                        </Link>

                        <h1 class="text-4xl sm:text-6xl font-bold text-white">
                            <span
                                class="bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent drop-shadow-lg">
                                KOSZYK ZAMÓWIEŃ
                            </span>
                        </h1>

                        <p class="text-gray-300 mt-2">
                            Zarządzaj swoimi strzałami na strzelnicy
                        </p>
                    </div>

                    <div class="text-right">
                        <div class="text-gray-400 text-sm mb-2">Razem strzałów: {{ totalShots }}</div>
                        <div class="text-gray-400 font-bold text-lg">{{ formatPrice(totalPrice) }} zł</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Club Member Toggle -->
        <div class="relative py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gray-800/80 backdrop-blur-sm rounded-xl p-6 border-2 border-gray-600/30">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-white font-bold text-lg">Członek Klubu Sagittarius</h3>
                            <p class="text-gray-400 text-sm mt-1">Zniżki na amunicję dla członków klubu</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                :checked="isClubMember"
                                @change="toggleClubMember"
                                class="sr-only peer"
                            >
                            <div
                                class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-500/25 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gray-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-300">
                                {{ isClubMember ? 'TAK' : 'NIE' }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="relative pb-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div v-if="cartItems.length === 0" class="text-center py-16">
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gray-800 rounded-full flex items-center justify-center border-2 border-gray-600/30">
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2 8h12l-2-8M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-3"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-semibold text-white mb-2">Koszyk jest pusty</h3>
                    <p class="text-gray-400 mb-8">Dodaj broń do koszyka, żeby zacząć rezerwację strzałów</p>
                    <Link
                        :href="route('guns.index')"
                        class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg transition-colors border-2 border-gray-500"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Przejdź do oferty broni
                    </Link>
                </div>

                <div v-else class="space-y-6">
                    <CartItem
                        v-for="item in cartItems"
                        :key="item.gun.id"
                        :item="item"
                        :is-club-member="isClubMember"
                        @update-quantity="updateQuantity"
                        @add-ammunition="addAmmunition"
                        @remove-ammunition="removeAmmunition"
                        @remove="removeItem"
                    />

                    <!-- Summary -->
                    <div class="bg-gray-800/80 backdrop-blur-sm rounded-xl p-6 border-2 border-gray-600/30">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-white font-bold text-xl">Podsumowanie zamówienia</h3>
                                <p class="text-gray-400 text-sm mt-1">Razem: {{ totalShots }} strzałów</p>
                            </div>
                            <div class="text-right">
                                <div class="text-3xl font-bold text-gray-400">{{ formatPrice(totalPrice) }} zł</div>
                                <p class="text-gray-400 text-sm mt-1">
                                    {{ isClubMember ? 'Cena klubowa' : 'Cena standardowa' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-4 mt-6">
                            <button
                                @click="clearCart"
                                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors border border-gray-500"
                            >
                                Wyczyść koszyk
                            </button>
                            <Link
                                :href="route('checkout.index')"
                                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg transition-colors border-2 border-gray-500 flex-1 text-center"
                            >
                                Złóż zamówienie
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Footer/>
    </div>
</template>

<script setup>
import {ref, computed} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import Footer from '../../Components/Common/Footer.vue';
import CartItem from '../../Components/Cart/CartItem.vue';

const props = defineProps({
    cart: {
        type: Object,
        default: () => ({})
    },
    guns: {
        type: Array,
        default: () => []
    },
    isClubMember: {
        type: Boolean,
        default: false
    }
});

const isClubMember = ref(props.isClubMember);

const cartItems = computed(() => {
    return Object.entries(props.cart).map(([gunId, cartItem]) => {
        const gun = props.guns.find(g => g.id == gunId);
        if (!gun) return null;

        // Get all ammunitions with their quantities
        const ammunitionItems = Object.entries(cartItem.ammunitions || {}).map(([ammoId, quantity]) => {
            const ammo = gun.caliber?.ammunitions?.find(a => a.id == ammoId);
            return ammo ? {ammunition: ammo, quantity} : null;
        }).filter(item => item !== null);

        return {
            gun,
            cartItem,
            ammunitionItems
        };
    }).filter(item => item !== null);
});

const totalShots = computed(() => {
    return cartItems.value.reduce((total, item) => {
        return total + item.ammunitionItems.reduce((itemTotal, ammoItem) => itemTotal + ammoItem.quantity, 0);
    }, 0);
});

const totalPrice = computed(() => {
    return cartItems.value.reduce((total, item) => {
        return total + item.ammunitionItems.reduce((itemTotal, ammoItem) => {
            const pricePerShot = isClubMember.value
                ? ammoItem.ammunition.club_price
                : ammoItem.ammunition.standard_price;
            return itemTotal + (pricePerShot * ammoItem.quantity);
        }, 0);
    }, 0);
});

function formatPrice(price) {
    if (price === null || price === undefined) return '0.00';

    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;

    if (isNaN(numericPrice)) return '0.00';

    return numericPrice.toFixed(2);
}

function updateQuantity(gunId, ammoId, action) {
    router.post(route('cart.update'), {
        gun_id: gunId,
        action: action,
        ammo_id: ammoId
    }, {
        preserveScroll: true
    });
}

function addAmmunition(gunId, ammoId, quantity) {
    router.post(route('cart.update'), {
        gun_id: gunId,
        action: 'add_ammo',
        ammo_id: ammoId,
        quantity: quantity
    }, {
        preserveScroll: true
    });
}

function removeAmmunition(gunId, ammoId) {
    router.post(route('cart.update'), {
        gun_id: gunId,
        action: 'remove_ammo',
        ammo_id: ammoId
    }, {
        preserveScroll: true
    });
}

function removeItem(gunId) {
    router.post(route('cart.update'), {
        gun_id: gunId,
        action: 'remove'
    }, {
        preserveScroll: true
    });
}

function toggleClubMember() {
    isClubMember.value = !isClubMember.value;
    router.post(route('cart.toggle-club-member'), {
        is_club_member: isClubMember.value
    }, {
        preserveScroll: true
    });
}

function clearCart() {
    if (confirm('Czy na pewno chcesz wyczyścić cały koszyk?')) {
        router.post(route('cart.clear'), {}, {
            preserveScroll: true
        });
    }
}
</script>
