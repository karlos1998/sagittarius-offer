<template>
    <div class="min-h-screen bg-white text-black">
        <SimpleNavbar :cart="cart" />
        <HeroSection />
        <FeaturesSection />
        <InstructorsSection :instructors="instructors" />
        <Footer />
    </div>
</template>

<script setup lang="ts">
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import FeaturesSection from '../../Components/Home/FeaturesSection.vue';
import HeroSection from '../../Components/Home/HeroSection.vue';
import InstructorsSection from '../../Components/Home/InstructorsSection.vue';
import type { CartMap, Instructor } from '@/types/storefront';

withDefaults(
    defineProps<{
        cart?: CartMap;
        instructors?: Instructor[];
    }>(),
    {
        cart: () => ({}),
        instructors: () => [],
    }
);
</script>
