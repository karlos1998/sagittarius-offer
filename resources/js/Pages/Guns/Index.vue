<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <GunsHeader/>
        <GunsGrid :guns="guns"/>
        <Footer/>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
import Footer from '../../Components/Common/Footer.vue';
import GunsGrid from '../../Components/Guns/GunsGrid.vue';
import GunsHeader from '../../Components/Guns/GunsHeader.vue';

defineProps({
    guns: {
        type: Array,
        default: () => []
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
