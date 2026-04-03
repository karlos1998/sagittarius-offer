<template>
    <div class="overflow-hidden rounded border border-black/30 bg-white">
        <!-- Header -->
        <div class="border-b border-black/20 bg-white p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">{{ item.gun.name }}</h3>
                    <p class="text-sm text-black/60">{{ item.gun.gun_type?.name }} • {{ item.gun.caliber?.name }}</p>
                    <p v-if="item.cartItem?.package_name" class="mt-1 inline-flex items-center gap-1 rounded border border-amber-300 bg-amber-100 px-2 py-0.5 text-xs font-medium text-black">
                        Pakiet: {{ item.cartItem.package_name }}
                    </p>
                </div>
                <button
                    @click="removeItem"
                    class="rounded p-2 text-black/50 transition-colors hover:bg-black hover:text-white"
                    title="Usuń całą broń z koszyka"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Ammunition Items -->
            <div class="space-y-4">
                <div
                    v-for="ammoItem in item.ammunitionItems"
                    :key="ammoItem.ammunition.id"
                    class="rounded border border-black/20 p-4"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="font-medium">{{ ammoItem.ammunition.name }}</h4>
                            <p class="text-sm text-black/70">
                                Cena: {{
                                    formatPrice(isClubMember ? ammoItem.ammunition.club_price : ammoItem.ammunition.standard_price)
                                }} zł/szt
                                <span class="text-black/50">({{ isClubMember ? 'klub' : 'standard' }})</span>
                            </p>
                        </div>
                        <button
                            @click="removeAmmo(ammoItem.ammunition.id, ammoItem.ammunition.name)"
                            class="rounded p-1 text-black/50 transition-colors hover:bg-black hover:text-white"
                            title="Usuń tę amunicję"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <button
                                @click="updateQuantity(ammoItem.ammunition.id, 'decrease')"
                                :disabled="ammoItem.quantity <= 5"
                                class="flex h-8 w-8 items-center justify-center rounded border border-black bg-white text-black transition-colors hover:bg-black hover:text-white disabled:cursor-not-allowed disabled:border-black/30 disabled:text-black/30"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                </svg>
                            </button>

                            <span class="min-w-[2rem] text-center text-lg font-semibold">
                                {{ ammoItem.quantity }}
                            </span>

                            <button
                                @click="updateQuantity(ammoItem.ammunition.id, 'increase')"
                                class="flex h-8 w-8 items-center justify-center rounded border border-black bg-white text-black transition-colors hover:bg-black hover:text-white"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>

                            <span class="ml-2 text-sm text-black/60">strzałów</span>
                        </div>

                        <div class="text-right">
                            <div class="font-semibold">
                                {{ formatPrice(ammoTotal(ammoItem)) }} zł
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add More Ammunition Button -->
            <div v-if="availableAmmunitions.length > 0" class="mt-6 border-t border-black/20 pt-4">
                <label class="mb-2 block font-medium">Dodaj więcej amunicji:</label>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="ammo in availableAmmunitions"
                        :key="ammo.id"
                        @click="addAmmunition(ammo.id)"
                        class="rounded border border-black/40 px-3 py-2 text-sm transition-colors hover:bg-black hover:text-white"
                    >
                        + {{ ammo.name }}
                    </button>
                </div>
            </div>

            <div v-if="item.cartItem?.package_guns?.length" class="mt-6 rounded border border-black/20 bg-black/[0.02] p-3">
                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-black/50">Broń w pakiecie</p>
                <p class="text-sm text-black/70">
                    {{ item.cartItem.package_guns.join(', ') }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import {computed} from 'vue';

const props = defineProps({
    item: {
        type: Object,
        required: true
    },
    isClubMember: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update-quantity', 'add-ammunition', 'remove-ammunition', 'remove']);

const availableAmmunitions = computed(() => {
    if (!props.item.gun || !props.item.gun.caliber) return [];

    return props.item.gun.caliber.ammunitions.filter(ammo => !props.item.ammunitionItems.some(item => item.ammunition.id === ammo.id));
});

const itemTotal = computed(() => {
    return props.item.ammunitionItems.reduce((total, ammoItem) => {
        const pricePerShot = props.isClubMember
            ? ammoItem.ammunition.club_price
            : ammoItem.ammunition.standard_price;

        return total + pricePerShot * ammoItem.quantity;
    }, 0);
});

function formatPrice(price) {
    if (price === null || price === undefined) return '0.00';

    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;

    if (isNaN(numericPrice)) return '0.00';

    return numericPrice.toFixed(2);
}

function ammoTotal(ammoItem) {
    const pricePerShot = props.isClubMember
        ? ammoItem.ammunition.club_price
        : ammoItem.ammunition.standard_price;

    return pricePerShot * ammoItem.quantity;
}

function updateQuantity(ammoId, action) {
    emit('update-quantity', props.item.gun.id, ammoId, action);
}

function addAmmunition(ammoId) {
    emit('add-ammunition', props.item.gun.id, ammoId);
}

function removeAmmo(ammoId, ammoName) {
    emit('remove-ammunition', props.item.gun.id, ammoId, ammoName);
}

function removeItem() {
    emit('remove', props.item.gun.id, props.item.gun.name);
}
</script>
