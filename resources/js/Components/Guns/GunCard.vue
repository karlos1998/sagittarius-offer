<template>
    <div
        class="bg-slate-800/50 backdrop-blur-sm rounded-xl overflow-hidden border border-slate-700/50 hover:border-orange-500/50 transition-all duration-300 hover:transform hover:scale-105">
        <!-- Gun Image -->
        <div class="aspect-video bg-slate-700 relative overflow-hidden">
            <div v-if="gun.photos && gun.photos.length > 0" class="w-full h-full">
                <img
                    :src="gun.photos[0]"
                    :alt="gun.name"
                    class="w-full h-full object-cover"
                    @error="handleImageError"
                />
            </div>
            <div v-else class="w-full h-full flex items-center justify-center">
                <svg class="w-16 h-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <!-- Gun Info -->
        <div class="p-6">
            <h3 class="text-xl font-semibold text-white mb-2">{{ gun.name }}</h3>

            <div class="space-y-2 mb-4">
                <div class="flex items-center text-slate-300">
                    <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2m-9 0h10m-9 0V1m10 3V1m0 3l1 1v16a2 2 0 01-2 2H6a2 2 0 01-2-2V8l1-1z"/>
                    </svg>
                    <span class="text-sm">{{ gun.gun_type?.name || 'Nieznany typ' }}</span>
                </div>

                <div class="flex items-center text-slate-300">
                    <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm">Kaliber: {{ gun.caliber?.name || 'Nieznany' }}</span>
                </div>
            </div>

            <div v-if="gun.description" class="text-slate-400 text-sm">
                {{ gun.description }}
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    gun: {
        type: Object,
        required: true
    }
});

function handleImageError(event) {
    event.target.style.display = 'none';
    const parent = event.target.parentElement;
    const fallback = parent.querySelector('.fallback-icon') || document.createElement('div');
    if (!fallback.classList.contains('fallback-icon')) {
        fallback.className = 'fallback-icon w-full h-full flex items-center justify-center';
        fallback.innerHTML = `
            <svg class="w-16 h-16 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        `;
        parent.appendChild(fallback);
    }
    fallback.style.display = 'flex';
}
</script>
