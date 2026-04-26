<template>
    <div class="min-h-screen bg-stone-950 text-white">
        <div class="border-b border-white/10 bg-stone-950/95 backdrop-blur">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-300/80">
                        Panel pracownika
                    </p>
                    <h1 class="mt-2 text-2xl font-semibold sm:text-3xl">
                        Zamówienia
                    </h1>
                    <p class="mt-1 text-sm text-white/60">
                        Lista od najnowszych. Możesz szybko wyszukać klienta, e-mail albo numer zamówienia.
                    </p>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center rounded-xl border border-white/15 px-4 py-2 text-sm font-semibold text-white transition hover:border-white/30 hover:bg-white/5"
                    @click="logout"
                >
                    Wyloguj
                </button>
            </div>
        </div>

        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-8 sm:px-6 lg:px-8">
            <div v-if="$page.props.flash?.success" class="rounded-2xl border border-emerald-400/30 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-100">
                {{ $page.props.flash.success }}
            </div>

            <section class="rounded-3xl border border-white/10 bg-white/5 p-4 shadow-2xl shadow-black/20 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-semibold text-white">
                            Wszystkie zamówienia
                        </h2>
                        <p class="text-sm text-white/50">
                            {{ orders.length }} {{ orders.length === 1 ? 'wynik' : 'wyników' }}
                        </p>
                    </div>

                    <div class="w-full lg:max-w-md">
                        <label for="search" class="mb-2 block text-xs font-semibold uppercase tracking-[0.25em] text-white/45">
                            Szukaj
                        </label>
                        <input
                            id="search"
                            v-model="search"
                            type="text"
                            placeholder="Numer, klient, e-mail..."
                            class="w-full rounded-2xl border border-white/10 bg-stone-900/80 px-4 py-3 text-sm text-white placeholder:text-white/35 focus:border-amber-300 focus:outline-none focus:ring-0"
                        >
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl shadow-black/20">
                <div v-if="orders.length === 0" class="px-6 py-14 text-center">
                    <p class="text-lg font-semibold text-white">
                        Brak pasujących zamówień
                    </p>
                    <p class="mt-2 text-sm text-white/55">
                        Spróbuj zmienić frazę wyszukiwania.
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-white/10">
                        <thead class="bg-white/5">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-white/45">
                                <th class="px-6 py-4">Zamówienie</th>
                                <th class="px-6 py-4">Klient</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Kwota</th>
                                <th class="px-6 py-4">Utworzono</th>
                                <th class="px-6 py-4 text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            <tr v-for="order in orders" :key="order.id" class="align-top transition hover:bg-white/[0.03]">
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-white">
                                        {{ order.order_number }}
                                    </div>
                                    <div class="mt-1 text-sm text-white/50">
                                        {{ order.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-medium text-white">
                                        {{ order.customer_full_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex rounded-full border border-amber-300/30 bg-amber-400/10 px-3 py-1 text-xs font-semibold text-amber-100">
                                            {{ order.status_label }}
                                        </span>
                                        <span class="inline-flex rounded-full border border-sky-300/30 bg-sky-400/10 px-3 py-1 text-xs font-semibold text-sky-100">
                                            {{ order.payment_status_label }}
                                        </span>
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="order.is_completed
                                                ? 'border border-emerald-300/30 bg-emerald-400/10 text-emerald-100'
                                                : 'border border-white/15 bg-white/5 text-white/70'"
                                        >
                                            {{ order.is_completed ? 'Zrealizowane' : 'Do realizacji' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm font-semibold text-white">
                                    {{ formatCurrency(order.total_amount) }}
                                </td>
                                <td class="px-6 py-5 text-sm text-white/60">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button
                                        v-if="!order.is_completed"
                                        type="button"
                                        class="inline-flex items-center rounded-xl border border-emerald-300/40 bg-emerald-400/10 px-4 py-2 text-sm font-semibold text-emerald-100 transition hover:border-emerald-300/60 hover:bg-emerald-400/20"
                                        @click="openCompletionModal(order)"
                                    >
                                        Zrealizuj
                                    </button>

                                    <span v-else class="text-sm font-medium text-emerald-200/80">
                                        Gotowe
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <ConfirmationModal :show="completionModalOpen" max-width="lg" @close="closeCompletionModal">
            <template #title>
                Potwierdzenie realizacji
            </template>

            <template #content>
                <p v-if="selectedOrder" class="text-sm leading-6 text-gray-600">
                    Czy na pewno chcesz zrealizować zamówienie
                    <strong>{{ selectedOrder.order_number }}</strong>
                    dla klienta
                    <strong>{{ selectedOrder.customer_full_name }}</strong>?
                </p>
            </template>

            <template #footer>
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <SecondaryButton :disabled="completeForm.processing" @click="closeCompletionModal">
                        Anuluj
                    </SecondaryButton>

                    <PrimaryButton :disabled="completeForm.processing" @click="completeSelectedOrder">
                        {{ completeForm.processing ? 'Zapisywanie...' : 'Tak, zrealizuj' }}
                    </PrimaryButton>
                </div>
            </template>
        </ConfirmationModal>
    </div>
</template>

<script setup lang="ts">
import { computed, onUnmounted, ref, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import type { PanelOrder } from '@/types/storefront';

const props = withDefaults(
    defineProps<{
        filters?: {
            search?: string;
        };
        orders?: PanelOrder[];
    }>(),
    {
        filters: () => ({
            search: '',
        }),
        orders: () => [],
    }
);

const search = ref(props.filters.search ?? '');
const completionModalOpen = ref(false);
const selectedOrder = ref<PanelOrder | null>(null);
const completeForm = useForm({});

let debounceTimeout: ReturnType<typeof setTimeout> | null = null;

const orders = computed(() => props.orders);

watch(search, (value) => {
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }

    debounceTimeout = setTimeout(() => {
        router.get(
            route('panel.index'),
            {
                search: value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['orders', 'filters'],
            }
        );
    }, 1000);
});

onUnmounted(() => {
    if (debounceTimeout) {
        clearTimeout(debounceTimeout);
    }
});

function openCompletionModal(order: PanelOrder): void {
    selectedOrder.value = order;
    completionModalOpen.value = true;
}

function closeCompletionModal(): void {
    if (completeForm.processing) {
        return;
    }

    completionModalOpen.value = false;
    selectedOrder.value = null;
}

function completeSelectedOrder(): void {
    if (!selectedOrder.value) {
        return;
    }

    completeForm.post(route('panel.orders.complete', selectedOrder.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeCompletionModal();
        },
    });
}

function logout(): void {
    router.post(route('logout'));
}

function formatCurrency(value: number | string): string {
    const amount = Number(value);

    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
    }).format(amount);
}

function formatDate(value: string | null | undefined): string {
    if (!value) {
        return '—';
    }

    return new Date(value).toLocaleString('pl-PL');
}
</script>
