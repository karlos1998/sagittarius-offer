<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
        <HeroSection/>
        <FeaturesSection/>
        <Footer/>

        <!-- Cart Button -->
        <CartButton :cart="cart"/>
    </div>
</template>

<script setup>
import Footer from '../../Components/Common/Footer.vue';
import FeaturesSection from '../../Components/Home/FeaturesSection.vue';
import HeroSection from '../../Components/Home/HeroSection.vue';
import CartButton from '../../Components/Common/CartButton.vue';

defineProps({
    cart: {
        type: Object,
        default: () => ({})
    }
});
</script>
