<template>
    <div class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-950">
        <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 to-black">
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
                <Link :href="route('cart.index')" class="inline-flex items-center text-gray-400 hover:text-white mb-4 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do koszyka
                </Link>

                <h1 class="text-4xl sm:text-5xl font-bold text-white">Finalizacja zamówienia</h1>
                <p class="text-gray-300 mt-2">Krok {{ stepLabel }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div v-if="$page.props.flash?.success" class="mb-6 bg-green-500/15 border border-green-500/40 text-green-200 px-4 py-3 rounded-lg">
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash?.error" class="mb-6 bg-red-500/15 border border-red-500/40 text-red-200 px-4 py-3 rounded-lg">
                {{ $page.props.flash.error }}
            </div>

            <div class="grid lg:grid-cols-2 gap-8">
                <div class="bg-gray-800/80 border border-gray-700 rounded-xl p-6">
                    <h2 class="text-xl font-semibold text-white mb-4">Podsumowanie</h2>

                    <template v-if="checkoutStep !== 'complete'">
                        <div class="space-y-4">
                            <div v-for="(item, index) in cartItems" :key="index" class="border border-gray-700 rounded-lg p-4">
                                <div class="text-white font-medium">{{ item.gun.name }}</div>
                                <ul class="mt-2 space-y-1 text-sm text-gray-300">
                                    <li v-for="ammo in item.ammunitionItems" :key="ammo.ammunition.id" class="flex justify-between gap-4">
                                        <span>{{ ammo.ammunition.name }} ({{ ammo.quantity }} strzałów)</span>
                                        <span>{{ formatPrice(ammo.total) }} zł</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </template>

                    <template v-else-if="order">
                        <div class="space-y-3 text-sm text-gray-300">
                            <div class="flex justify-between gap-4">
                                <span>Numer zamówienia</span>
                                <span class="text-white font-semibold">{{ order.order_number }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>E-mail</span>
                                <span class="text-white">{{ order.email }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Adres</span>
                                <span class="text-white text-right">{{ order.full_address }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Forma płatności</span>
                                <span class="text-white">{{ order.payment_method_label }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Status płatności</span>
                                <span class="text-white">{{ order.payment_status_label }}</span>
                            </div>
                        </div>
                    </template>

                    <div class="mt-6 border-t border-gray-700 pt-4 space-y-2 text-gray-300">
                        <div class="flex justify-between gap-4">
                            <span>Łącznie strzałów</span>
                            <span class="text-white font-medium">{{ totalShots }}</span>
                        </div>
                        <div class="flex justify-between gap-4 text-lg">
                            <span>Do zapłaty</span>
                            <span class="text-white font-bold">{{ formatPrice(totalPrice) }} zł</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-800/80 border border-gray-700 rounded-xl p-6">
                    <template v-if="checkoutStep === 'details'">
                        <h2 class="text-xl font-semibold text-white mb-4">Dane zamawiającego</h2>

                        <form @submit.prevent="submitOrder" class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Imię</label>
                                <input v-model="detailsForm.first_name" type="text" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                <InputError :message="detailsForm.errors.first_name" class="mt-2"/>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Nazwisko</label>
                                <input v-model="detailsForm.last_name" type="text" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                <InputError :message="detailsForm.errors.last_name" class="mt-2"/>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Ulica</label>
                                <input v-model="detailsForm.street" type="text" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                <InputError :message="detailsForm.errors.street" class="mt-2"/>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Numer domu</label>
                                    <input v-model="detailsForm.house_number" type="text" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                    <InputError :message="detailsForm.errors.house_number" class="mt-2"/>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Numer mieszkania (opcjonalnie)</label>
                                    <input v-model="detailsForm.apartment_number" type="text" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                    <InputError :message="detailsForm.errors.apartment_number" class="mt-2"/>
                                </div>
                            </div>

                            <div class="grid sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Kod pocztowy</label>
                                    <input v-model="detailsForm.postal_code" type="text" placeholder="00-000" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                    <InputError :message="detailsForm.errors.postal_code" class="mt-2"/>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-300 mb-1">Miasto</label>
                                    <input v-model="detailsForm.city" type="text" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                    <InputError :message="detailsForm.errors.city" class="mt-2"/>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-300 mb-1">E-mail</label>
                                <input v-model="detailsForm.email" type="email" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500"/>
                                <InputError :message="detailsForm.errors.email" class="mt-2"/>
                            </div>

                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Forma płatności</label>
                                <select v-model="detailsForm.payment_method" class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white focus:border-gray-500 focus:ring-gray-500">
                                    <option v-for="method in paymentMethods" :key="method.value" :value="method.value">
                                        {{ method.label }}
                                    </option>
                                </select>
                                <InputError :message="detailsForm.errors.payment_method" class="mt-2"/>
                            </div>

                            <button
                                type="submit"
                                :disabled="detailsForm.processing"
                                class="w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg border border-gray-500 disabled:opacity-60"
                            >
                                {{ detailsForm.processing ? 'Zapisywanie...' : 'Zapisz i wyślij kod' }}
                            </button>
                        </form>
                    </template>

                    <template v-else-if="checkoutStep === 'verify' && order">
                        <h2 class="text-xl font-semibold text-white mb-4">Weryfikacja e-mail</h2>
                        <p class="text-gray-300 mb-1">Kod został wysłany na: <strong>{{ order.email }}</strong></p>
                        <p v-if="order.verification_code_expires_at" class="text-gray-400 text-sm mb-4">
                            Kod jest ważny {{ order.verification_code_valid_for_minutes }} minut. Ważny do: {{ formatDate(order.verification_code_expires_at) }}
                        </p>

                        <form @submit.prevent="verifyCode" class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-300 mb-1">Kod weryfikacyjny</label>
                                <input
                                    v-model="verifyForm.code"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="6"
                                    class="w-full rounded-lg bg-gray-900 border border-gray-700 text-white tracking-[0.4em] text-center text-xl focus:border-gray-500 focus:ring-gray-500"
                                    placeholder="000000"
                                />
                                <InputError :message="verifyForm.errors.code" class="mt-2"/>
                            </div>

                            <button
                                type="submit"
                                :disabled="verifyForm.processing"
                                class="w-full px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg border border-gray-500 disabled:opacity-60"
                            >
                                {{ verifyForm.processing ? 'Weryfikacja...' : 'Przejdź dalej' }}
                            </button>
                        </form>

                        <button
                            v-if="isCodeExpired"
                            type="button"
                            :disabled="resendForm.processing"
                            @click="resendCode"
                            class="mt-3 w-full px-6 py-3 bg-gray-900 hover:bg-gray-950 text-white font-semibold rounded-lg border border-gray-600 disabled:opacity-60"
                        >
                            {{ resendForm.processing ? 'Wysyłanie nowego kodu...' : 'Wyślij nowy kod' }}
                        </button>
                    </template>

                    <template v-else-if="checkoutStep === 'complete' && order">
                        <h2 class="text-xl font-semibold text-white mb-4">Zamówienie gotowe</h2>
                        <p class="text-gray-300 mb-6">
                            Zamówienie zostało potwierdzone. Wysłaliśmy PDF na adres e-mail i możesz pobrać go również tutaj.
                        </p>

                        <a
                            v-if="order.download_url"
                            :href="order.download_url"
                            class="inline-flex w-full justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg border border-gray-500"
                        >
                            Pobierz PDF zamówienia
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onUnmounted, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    cart: {
        type: Object,
        default: () => ({}),
    },
    guns: {
        type: Array,
        default: () => [],
    },
    isClubMember: {
        type: Boolean,
        default: false,
    },
    checkoutStep: {
        type: String,
        default: 'details',
    },
    paymentMethods: {
        type: Array,
        default: () => [],
    },
    order: {
        type: Object,
        default: null,
    },
});

const detailsForm = useForm({
    first_name: '',
    last_name: '',
    street: '',
    house_number: '',
    apartment_number: '',
    postal_code: '',
    city: '',
    email: '',
    payment_method: props.paymentMethods[0]?.value ?? 'pay_on_site',
});

const verifyForm = useForm({
    code: '',
});

const resendForm = useForm({});
const currentTimestamp = ref(Date.now());
const currentTimestampInterval = setInterval(() => {
    currentTimestamp.value = Date.now();
}, 1000);

onUnmounted(() => {
    clearInterval(currentTimestampInterval);
});

const stepLabel = computed(() => {
    if (props.checkoutStep === 'verify') {
        return '2/3 - Weryfikacja';
    }

    if (props.checkoutStep === 'complete') {
        return '3/3 - Gotowe';
    }

    return '1/3 - Dane zamawiającego';
});

const cartItems = computed(() => {
    return Object.entries(props.cart)
        .map(([gunId, cartItem]) => {
            const gun = props.guns.find((item) => item.id == gunId);

            if (!gun) {
                return null;
            }

            const ammunitionItems = Object.entries(cartItem.ammunitions ?? {})
                .map(([ammoId, quantity]) => {
                    const ammunition = gun.caliber?.ammunitions?.find((item) => item.id == ammoId);

                    if (!ammunition) {
                        return null;
                    }

                    const pricePerShot = props.isClubMember
                        ? Number(ammunition.club_price ?? 0)
                        : Number(ammunition.standard_price ?? 0);
                    const shots = Number(quantity);

                    return {
                        ammunition,
                        quantity: shots,
                        total: pricePerShot * shots,
                    };
                })
                .filter((item) => item !== null);

            return {
                gun,
                ammunitionItems,
            };
        })
        .filter((item) => item !== null);
});

const totalShots = computed(() => {
    if (props.checkoutStep === 'complete' && props.order) {
        return Number(props.order.total_shots ?? 0);
    }

    return cartItems.value.reduce((total, item) => {
        return total + item.ammunitionItems.reduce((itemTotal, ammoItem) => itemTotal + ammoItem.quantity, 0);
    }, 0);
});

const totalPrice = computed(() => {
    if (props.checkoutStep === 'complete' && props.order) {
        return Number(props.order.total_amount ?? 0);
    }

    return cartItems.value.reduce((total, item) => {
        return total + item.ammunitionItems.reduce((itemTotal, ammoItem) => itemTotal + ammoItem.total, 0);
    }, 0);
});

const isCodeExpired = computed(() => {
    if (!props.order?.verification_code_expires_at) {
        return false;
    }

    return new Date(props.order.verification_code_expires_at).getTime() <= currentTimestamp.value;
});

function submitOrder() {
    detailsForm.post(route('checkout.store'));
}

function verifyCode() {
    verifyForm.post(route('checkout.verify'));
}

function resendCode() {
    resendForm.post(route('checkout.resend-code'));
}

function formatPrice(value) {
    const numericValue = Number(value ?? 0);

    if (Number.isNaN(numericValue)) {
        return '0.00';
    }

    return numericValue.toFixed(2);
}

function formatDate(value) {
    if (!value) {
        return '';
    }

    return new Date(value).toLocaleString('pl-PL');
}
</script>
