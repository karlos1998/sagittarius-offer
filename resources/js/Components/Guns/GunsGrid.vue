<template>
    <div class="relative py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="guns.length === 0" class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-slate-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-white mb-2">Brak dostępnej broni</h3>
                <p class="text-slate-400">Aktualnie nie mamy dostępnej broni w ofercie.</p>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <GunCard
                    v-for="gun in guns"
                    :key="gun.id"
                    :gun="gun"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import GunCard from './GunCard.vue';

defineProps({
    guns: {
        type: Array,
        default: () => []
    }
});
</script>
