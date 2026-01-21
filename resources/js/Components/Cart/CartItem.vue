<template>
    <div class="bg-gray-800/80 backdrop-blur-sm rounded-xl overflow-hidden border-2 border-red-600/30">
        <!-- Header -->
        <div class="bg-gradient-to-r from-red-600/20 to-yellow-600/20 p-4 border-b border-red-600/30">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-white font-bold text-lg">{{ item.gun.name }}</h3>
                    <p class="text-green-200 text-sm">{{ item.gun.gun_type?.name }} • {{ item.gun.caliber?.name }}</p>
                </div>
                <button
                    @click="removeItem"
                    class="text-red-400 hover:text-red-300 transition-colors p-2 hover:bg-red-600/20 rounded-lg"
                    title="Usuń całą broń z koszyka"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                    class="bg-gray-700/50 rounded-lg p-4 border border-red-600/20"
                >
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h4 class="text-white font-medium">{{ ammoItem.ammunition.name }}</h4>
                            <p class="text-green-300 text-sm">
                                Cena: {{
                                    formatPrice(isClubMember ? ammoItem.ammunition.club_price : ammoItem.ammunition.standard_price)
                                }} zł/szt
                                <span class="text-yellow-400">({{ isClubMember ? 'klub' : 'standard' }})</span>
                            </p>
                        </div>
                        <button
                            @click="removeAmmo(ammoItem.ammunition.id)"
                            class="text-red-400 hover:text-red-300 transition-colors p-1 hover:bg-red-600/20 rounded"
                            title="Usuń tę amunicję"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Quantity Controls -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <button
                                @click="updateQuantity(ammoItem.ammunition.id, 'decrease')"
                                :disabled="ammoItem.quantity <= 5"
                                class="w-8 h-8 bg-red-600 hover:bg-red-700 disabled:bg-gray-600 text-white rounded flex items-center justify-center transition-colors border border-red-500 disabled:border-gray-500"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </button>

                            <span class="text-white font-bold text-lg min-w-[2rem] text-center">
                                {{ ammoItem.quantity }}
                            </span>

                            <button
                                @click="updateQuantity(ammoItem.ammunition.id, 'increase')"
                                class="w-8 h-8 bg-red-600 hover:bg-red-700 text-white rounded flex items-center justify-center transition-colors border border-red-500"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>

                            <span class="text-green-300 text-sm ml-2">strzałów</span>
                        </div>

                        <div class="text-right">
                            <div class="text-yellow-400 font-bold">
                                {{ formatPrice(ammoTotal(ammoItem)) }} zł
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add More Ammunition Button -->
            <div v-if="availableAmmunitions.length > 0" class="mt-6 pt-4 border-t border-red-600/30">
                <label class="block text-green-200 font-medium mb-2">Dodaj więcej amunicji:</label>
                <div class="flex flex-wrap gap-2">
                    <button
                        v-for="ammo in availableAmmunitions"
                        :key="ammo.id"
                        @click="addAmmunition(ammo.id)"
                        class="px-3 py-2 bg-red-600/20 hover:bg-red-600/40 text-red-300 hover:text-red-200 rounded border border-red-600/30 hover:border-red-500/50 transition-colors text-sm"
                    >
                        + {{ ammo.name }}
                    </button>
                </div>
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

function removeAmmo(ammoId) {
    if (confirm(`Czy na pewno chcesz usunąć ${ammoId} z koszyka?`)) {
        emit('remove-ammunition', props.item.gun.id, ammoId);
    }
}

function removeItem() {
    if (confirm(`Czy na pewno chcesz usunąć ${props.item.gun.name} z koszyka?`)) {
        emit('remove', props.item.gun.id);
    }
}
</script>
