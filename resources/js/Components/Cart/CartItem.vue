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
                    title="Usuń z koszyka"
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
            <!-- Ammunition Selection -->
            <div class="mb-6">
                <label class="block text-green-200 font-medium mb-2">Wybierz amunicję:</label>
                <select
                    :value="item.cartItem.ammo_id"
                    @change="changeAmmo($event.target.value)"
                    class="w-full bg-gray-700 border border-red-600/30 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-red-500"
                >
                    <option
                        v-for="ammo in item.gun.caliber?.ammunitions || []"
                        :key="ammo.id"
                        :value="ammo.id"
                    >
                        {{ ammo.name }} -
                        {{ isClubMember ? ammo.club_price : ammo.standard_price }}zł/szt
                        ({{ isClubMember ? 'klub' : 'standard' }})
                    </option>
                </select>
            </div>

            <!-- Quantity Controls -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <label class="block text-green-200 font-medium mb-2">Liczba strzałów:</label>
                    <div class="flex items-center space-x-3">
                        <button
                            @click="updateQuantity('decrease')"
                            :disabled="item.cartItem.quantity <= 5"
                            class="w-10 h-10 bg-red-600 hover:bg-red-700 disabled:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-colors border border-red-500 disabled:border-gray-500"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>

                        <span class="text-white font-bold text-xl min-w-[3rem] text-center">
                            {{ item.cartItem.quantity }}
                        </span>

                        <button
                            @click="updateQuantity('increase')"
                            class="w-10 h-10 bg-red-600 hover:bg-red-700 text-white rounded-lg flex items-center justify-center transition-colors border border-red-500"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="text-right">
                    <div class="text-green-200 text-sm">Cena za strzał:</div>
                    <div class="text-yellow-400 font-bold">
                        {{
                            item.selectedAmmo ? formatPrice(isClubMember ? item.selectedAmmo.club_price : item.selectedAmmo.standard_price) : '0.00'
                        }} zł
                    </div>
                </div>
            </div>

            <!-- Item Total -->
            <div class="bg-red-900/20 border border-red-600/30 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-green-200 font-medium">{{ item.gun.name }}</div>
                        <div class="text-green-300 text-sm">{{ item.selectedAmmo?.name || 'Brak amunicji' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-white text-sm">{{ item.cartItem.quantity }} strzałów</div>
                        <div class="text-yellow-400 font-bold text-lg">
                            {{ formatPrice(itemTotal) }} zł
                        </div>
                    </div>
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

const emit = defineEmits(['update-quantity', 'change-ammo', 'remove']);

const itemTotal = computed(() => {
    if (!props.item.selectedAmmo) return 0;

    const pricePerShot = props.isClubMember
        ? props.item.selectedAmmo.club_price
        : props.item.selectedAmmo.standard_price;

    return pricePerShot * props.item.cartItem.quantity;
});

function formatPrice(price) {
    if (price === null || price === undefined) return '0.00';

    const numericPrice = typeof price === 'string' ? parseFloat(price) : price;

    if (isNaN(numericPrice)) return '0.00';

    return numericPrice.toFixed(2);
}

function updateQuantity(action) {
    emit('update-quantity', props.item.gun.id, action);
}

function changeAmmo(ammoId) {
    emit('change-ammo', props.item.gun.id, ammoId);
}

function removeItem() {
    if (confirm(`Czy na pewno chcesz usunąć ${props.item.gun.name} z koszyka?`)) {
        emit('remove', props.item.gun.id);
    }
}
</script>
