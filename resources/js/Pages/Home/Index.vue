<template>
    <div class="min-h-screen bg-white text-black">
        <SimpleNavbar :cart="cart" />
        <HeroSection />
        <FeaturesSection />
        <Footer />
    </div>
</template>

<script setup>
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import FeaturesSection from '../../Components/Home/FeaturesSection.vue';
import HeroSection from '../../Components/Home/HeroSection.vue';

defineProps({
    cart: {
        type: Object,
        default: () => ({}),
    },
});
</script>
