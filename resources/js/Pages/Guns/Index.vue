<template>
    <div class="min-h-screen bg-white text-black">
        <SimpleNavbar :cart="cart" />
        <GunsHeader />
        <GunsGrid :guns="guns" :gun-types="gunTypes" :cart="cart" @add-to-cart="handleAddToCart" />
        <Footer />
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import GunsGrid from '../../Components/Guns/GunsGrid.vue';
import GunsHeader from '../../Components/Guns/GunsHeader.vue';

defineProps({
    guns: {
        type: Array,
        default: () => [],
    },
    gunTypes: {
        type: Array,
        default: () => [],
    },
    cart: {
        type: Object,
        default: () => ({}),
    },
});

function handleAddToCart(gunId) {
    router.post(
        route('cart.add'),
        {
            gun_id: gunId,
        },
        {
            preserveScroll: true,
        }
    );
}
</script>
