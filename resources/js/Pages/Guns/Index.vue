<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <GunsHeader/>
        <GunsGrid :guns="guns" :gun-types="gunTypes" :cart="cart" @add-to-cart="handleAddToCart"/>
        <Footer/>
        <!-- Cart Button -->
        <CartButton :cart="cart"/>
    </div>
</template>

<script setup>
import {computed} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import Footer from '../../Components/Common/Footer.vue';
import GunsGrid from '../../Components/Guns/GunsGrid.vue';
import GunsHeader from '../../Components/Guns/GunsHeader.vue';
import CartButton from '../../Components/Common/CartButton.vue';

defineProps({
    guns: {
        type: Array,
        default: () => []
    },
    gunTypes: {
        type: Array,
        default: () => []
    },
    cart: {
        type: Object,
        default: () => ({})
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

function handleAddToCart(gunId) {
    router.post(route('cart.add'), {
        gun_id: gunId
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Update cart data after successful addition
            // This will be handled automatically by Inertia
        }
    });
}
</script>
