<template>
    <div class="min-h-screen bg-white text-black">
        <div class="sticky top-0 z-50 border-b border-black bg-black text-white">
            <div class="mx-auto flex h-12 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-amber-300/80">
                        Panel pracownika
                    </p>
                    <h1 class="text-sm font-semibold sm:text-base">
                        Zamówienia
                    </h1>
                </div>

                <button
                    type="button"
                    class="inline-flex items-center rounded border border-white/30 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white transition-colors hover:border-white hover:bg-white hover:text-black"
                    @click="logout"
                >
                    Wyloguj
                </button>
            </div>
        </div>

        <div class="mx-auto flex w-full max-w-7xl flex-col gap-6 px-4 py-8 sm:px-6 lg:px-8">
            <div class="border-b border-black/20 bg-white pb-8 pt-2">
                <div class="pt-8 text-center">
                    <div class="mx-auto mb-6 inline-flex items-center rounded border border-black/20 px-3 py-1 text-xs font-medium uppercase tracking-wide text-black/70">
                        <span class="mr-2 inline-block h-2 w-2 rounded-full bg-amber-300" />
                        Strefa pracownika
                    </div>

                    <h2 class="text-3xl font-semibold sm:text-4xl">
                        Zamówienia klientów
                    </h2>
                    <p class="mx-auto mt-3 max-w-2xl text-sm text-black/60 sm:text-base">
                        Tu masz szybki podgląd wszystkich zamówień. Wyszukasz klienta po numerze, mailu, imieniu albo nazwisku i od razu oznaczysz zamówienie jako zrealizowane.
                    </p>
                </div>
            </div>

            <div v-if="$page.props.flash?.success" class="rounded border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ $page.props.flash.success }}
            </div>

            <section class="rounded border border-black/20 bg-white p-4 sm:p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-lg font-semibold text-black">
                            Wszystkie zamówienia
                        </h2>
                        <p class="text-sm text-black/55">
                            {{ orders.length }} {{ orders.length === 1 ? 'wynik' : 'wyników' }}
                        </p>
                    </div>

                    <div class="w-full lg:max-w-md">
                        <label for="search" class="mb-2 block text-xs font-semibold uppercase tracking-[0.25em] text-black/50">
                            Szukaj
                        </label>
                        <input
                            id="search"
                            v-model="search"
                            type="text"
                            placeholder="Numer, klient, e-mail..."
                            class="w-full rounded border border-black/25 bg-white px-4 py-3 text-sm text-black placeholder:text-black/35 focus:border-black focus:outline-none focus:ring-0"
                        >
                    </div>
                </div>
            </section>

            <section class="overflow-hidden rounded border border-black/20 bg-white">
                <div v-if="orders.length === 0" class="px-6 py-14 text-center">
                    <p class="text-lg font-semibold text-black">
                        Brak pasujących zamówień
                    </p>
                    <p class="mt-2 text-sm text-black/55">
                        Spróbuj zmienić frazę wyszukiwania.
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-black/10">
                        <thead class="bg-black/[0.03]">
                            <tr class="text-left text-xs font-semibold uppercase tracking-[0.2em] text-black/50">
                                <th class="px-6 py-4">Zamówienie</th>
                                <th class="px-6 py-4">Klient</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Kwota</th>
                                <th class="px-6 py-4">Utworzono</th>
                                <th class="px-6 py-4 text-right">Akcja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-black/10">
                            <tr v-for="order in orders" :key="order.id" class="align-top transition hover:bg-black/[0.02]">
                                <td class="px-6 py-5">
                                    <div class="font-semibold text-black">
                                        {{ order.order_number }}
                                    </div>
                                    <div class="mt-1 text-sm text-black/55">
                                        {{ order.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="font-medium text-black">
                                        {{ order.customer_full_name }}
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex rounded-full border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-900">
                                            {{ order.status_label }}
                                        </span>
                                        <span class="inline-flex rounded-full border border-sky-300 bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-900">
                                            {{ order.payment_status_label }}
                                        </span>
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                            :class="order.is_completed
                                                ? 'border border-emerald-300 bg-emerald-50 text-emerald-900'
                                                : 'border border-black/15 bg-black/[0.03] text-black/70'"
                                        >
                                            {{ order.is_completed ? 'Zrealizowane' : 'Do realizacji' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-sm font-semibold text-black">
                                    {{ formatCurrency(order.total_amount) }}
                                </td>
                                <td class="px-6 py-5 text-sm text-black/60">
                                    {{ formatDate(order.created_at) }}
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <button
                                        v-if="!order.is_completed"
                                        type="button"
                                        class="inline-flex items-center rounded border border-black bg-black px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-black"
                                        @click="openCompletionModal(order)"
                                    >
                                        Zrealizuj
                                    </button>

                                    <span v-else class="text-sm font-medium text-emerald-700">
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
