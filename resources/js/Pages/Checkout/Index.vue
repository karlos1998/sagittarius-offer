<template>
    <div class="min-h-screen bg-white text-black">
        <SimpleNavbar :cart="cart" />

        <div class="border-b border-black/20 bg-white py-8">
            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                <Link :href="route('cart.index')" class="inline-flex items-center text-sm text-black/70 hover:text-black">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Powrót do koszyka
                </Link>

                <h1 class="mt-2 inline-flex items-center gap-2 text-3xl font-semibold">
                    <span class="inline-block h-2 w-2 rounded-full bg-amber-300" />
                    Finalizacja zamówienia
                </h1>
                <p class="mt-1 text-sm text-black/60">Krok {{ stepLabel }}</p>
            </div>
        </div>

        <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div v-if="$page.props.flash?.success" class="mb-6 rounded border border-black bg-black px-4 py-3 text-sm text-white">
                {{ $page.props.flash.success }}
            </div>

            <div v-if="$page.props.flash?.error" class="mb-6 rounded border border-black px-4 py-3 text-sm text-black">
                {{ $page.props.flash.error }}
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded border border-black/30 bg-white p-6">
                    <h2 class="mb-4 text-xl font-semibold">Podsumowanie</h2>

                    <template v-if="checkoutStep !== 'complete'">
                        <div class="space-y-4">
                            <div v-for="(item, index) in cartItems" :key="index" class="rounded border border-black/20 p-4">
                                <div class="font-medium">{{ item.gun.name }}</div>
                                <p
                                    v-if="item.cartItem?.package_name"
                                    class="mt-1 inline-flex items-center gap-1 rounded border border-amber-300 bg-amber-100 px-2 py-0.5 text-xs font-medium text-black"
                                >
                                    Pakiet: {{ item.cartItem.package_name }}
                                </p>
                                <p v-if="item.cartItem?.package_guns?.length" class="mt-1 text-xs text-black/60">
                                    Broń w pakiecie: {{ item.cartItem.package_guns.join(', ') }}
                                </p>
                                <ul class="mt-2 space-y-1 text-sm text-black/70">
                                    <li v-for="ammo in item.ammunitionItems" :key="ammo.ammunition.id" class="flex justify-between gap-4">
                                        <span>{{ ammo.ammunition.name }} ({{ ammo.quantity }} strzałów)</span>
                                        <span>{{ formatPrice(ammo.total) }} zł</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </template>

                    <template v-else-if="order">
                        <div class="space-y-3 text-sm text-black/70">
                            <div class="flex justify-between gap-4">
                                <span>Numer zamówienia</span>
                                <span class="font-semibold text-black">{{ order.order_number }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>E-mail</span>
                                <span class="text-black">{{ order.email }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Adres</span>
                                <span class="text-right text-black">{{ order.full_address }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Forma płatności</span>
                                <span class="text-black">{{ order.payment_method_label }}</span>
                            </div>
                            <div class="flex justify-between gap-4">
                                <span>Status płatności</span>
                                <span class="text-black">{{ order.payment_status_label }}</span>
                            </div>
                        </div>
                    </template>

                    <div class="mt-6 space-y-2 border-t border-black/20 pt-4 text-sm">
                        <div class="flex justify-between gap-4">
                            <span>Łącznie strzałów</span>
                            <span class="font-medium">{{ totalShots }}</span>
                        </div>
                        <div class="flex justify-between gap-4 text-lg">
                            <span>Do zapłaty</span>
                            <span class="font-semibold">{{ formatPrice(totalPrice) }} zł</span>
                        </div>
                    </div>
                </div>

                <div class="rounded border border-black/30 bg-white p-6">
                    <template v-if="checkoutStep === 'details'">
                        <h2 class="mb-4 text-xl font-semibold">Dane zamawiającego</h2>

                        <form @submit.prevent="submitOrder" class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm">Imię</label>
                                <input v-model="detailsForm.first_name" type="text" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                <InputError :message="detailsForm.errors.first_name" class="mt-2" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm">Nazwisko</label>
                                <input v-model="detailsForm.last_name" type="text" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                <InputError :message="detailsForm.errors.last_name" class="mt-2" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm">Ulica</label>
                                <input v-model="detailsForm.street" type="text" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                <InputError :message="detailsForm.errors.street" class="mt-2" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm">Numer domu</label>
                                    <input v-model="detailsForm.house_number" type="text" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                    <InputError :message="detailsForm.errors.house_number" class="mt-2" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm">Numer mieszkania (opcjonalnie)</label>
                                    <input v-model="detailsForm.apartment_number" type="text" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                    <InputError :message="detailsForm.errors.apartment_number" class="mt-2" />
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1 block text-sm">Kod pocztowy</label>
                                    <input v-model="detailsForm.postal_code" type="text" placeholder="00-000" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                    <InputError :message="detailsForm.errors.postal_code" class="mt-2" />
                                </div>
                                <div>
                                    <label class="mb-1 block text-sm">Miasto</label>
                                    <input v-model="detailsForm.city" type="text" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                    <InputError :message="detailsForm.errors.city" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <label class="mb-1 block text-sm">E-mail</label>
                                <input v-model="detailsForm.email" type="email" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none" />
                                <InputError :message="detailsForm.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm">Forma płatności</label>
                                <select v-model="detailsForm.payment_method" class="w-full rounded border border-black/30 px-3 py-2 text-sm focus:border-black focus:outline-none">
                                    <option v-for="method in paymentMethods" :key="method.value" :value="method.value">
                                        {{ method.label }}
                                    </option>
                                </select>
                                <InputError :message="detailsForm.errors.payment_method" class="mt-2" />
                            </div>

                            <button
                                type="submit"
                                :disabled="detailsForm.processing"
                                class="w-full rounded border border-black bg-black px-5 py-2 text-sm font-medium text-white hover:bg-white hover:text-black disabled:opacity-60"
                            >
                                {{ detailsForm.processing ? 'Zapisywanie...' : 'Zapisz i wyślij kod' }}
                            </button>
                        </form>
                    </template>

                    <template v-else-if="checkoutStep === 'verify' && order">
                        <h2 class="mb-4 text-xl font-semibold">Weryfikacja e-mail</h2>
                        <p class="mb-1 text-sm">Kod został wysłany na: <strong>{{ order.email }}</strong></p>
                        <p v-if="order.verification_code_expires_at" class="mb-4 text-xs text-black/60">
                            Kod jest ważny {{ order.verification_code_valid_for_minutes }} minut. Ważny do: {{ formatDate(order.verification_code_expires_at) }}
                        </p>

                        <form @submit.prevent="verifyCode" class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm">Kod weryfikacyjny</label>
                                <input
                                    v-model="verifyForm.code"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="6"
                                    class="w-full rounded border border-black/30 px-3 py-2 text-center text-xl tracking-[0.35em] focus:border-black focus:outline-none"
                                    placeholder="000000"
                                />
                                <InputError :message="verifyForm.errors.code" class="mt-2" />
                            </div>

                            <button
                                type="submit"
                                :disabled="verifyForm.processing"
                                class="w-full rounded border border-black bg-black px-5 py-2 text-sm font-medium text-white hover:bg-white hover:text-black disabled:opacity-60"
                            >
                                {{ verifyForm.processing ? 'Weryfikacja...' : 'Przejdź dalej' }}
                            </button>
                        </form>

                        <button
                            v-if="isCodeExpired"
                            type="button"
                            :disabled="resendForm.processing"
                            @click="resendCode"
                            class="mt-3 w-full rounded border border-black px-5 py-2 text-sm font-medium hover:bg-black hover:text-white disabled:opacity-60"
                        >
                            {{ resendForm.processing ? 'Wysyłanie nowego kodu...' : 'Wyślij nowy kod' }}
                        </button>
                    </template>

                    <template v-else-if="checkoutStep === 'complete' && order">
                        <h2 class="mb-4 text-xl font-semibold">Zamówienie gotowe</h2>
                        <p class="mb-6 text-sm text-black/70">
                            Zamówienie zostało potwierdzone. Wysłaliśmy PDF na adres e-mail i możesz pobrać go również tutaj.
                        </p>

                        <div v-if="order.items?.length" class="mb-6 rounded border border-black/20 p-4">
                            <h3 class="mb-2 text-sm font-semibold uppercase tracking-wide text-black/60">Pozycje zamówienia</h3>
                            <ul class="space-y-2 text-sm">
                                <li v-for="(item, index) in order.items" :key="index" class="border-b border-black/10 pb-2 last:border-0 last:pb-0">
                                    <div class="font-medium">{{ item.gun_name }} / {{ item.ammunition_name }}</div>
                                    <div v-if="item.gun_package_name" class="text-black/70">
                                        Pakiet: {{ item.gun_package_name }}
                                        <span v-if="item.gun_package_guns_summary">({{ item.gun_package_guns_summary }})</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <a
                            v-if="order.download_url"
                            :href="order.download_url"
                            class="inline-flex w-full justify-center rounded border border-black bg-black px-5 py-2 text-sm font-medium text-white hover:bg-white hover:text-black"
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
import SimpleNavbar from '@/Components/Common/SimpleNavbar.vue';

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
                cartItem,
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
