<template>
    <div class="min-h-screen bg-white text-black">
        <Head title="Strzelnica dla każdego, bez zapisów" />
        <SimpleNavbar :cart="cart" />
        <RangeStatusHero :schedule="schedule" :location="location" />
        <PathsSection :permit-help-url="location.permit_help_url" />
        <FirstVisitSection />
        <PricesSection :highlights="priceHighlights" />
        <InstructorsSection :instructors="instructors" />
        <KidsSection />
        <FaqSection :permit-help-url="location.permit_help_url" />
        <VisitSection :location="location" :schedule="schedule" />
        <Footer />
    </div>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import FaqSection from '../../Components/Home/FaqSection.vue';
import FirstVisitSection from '../../Components/Home/FirstVisitSection.vue';
import InstructorsSection from '../../Components/Home/InstructorsSection.vue';
import KidsSection from '../../Components/Home/KidsSection.vue';
import PathsSection from '../../Components/Home/PathsSection.vue';
import PricesSection from '../../Components/Home/PricesSection.vue';
import RangeStatusHero from '../../Components/Home/RangeStatusHero.vue';
import VisitSection from '../../Components/Home/VisitSection.vue';
import type { CartMap, Instructor, RangeLocation, RangeScheduleData, ShotPriceHighlight } from '@/types/storefront';

withDefaults(
    defineProps<{
        cart?: CartMap;
        instructors?: Instructor[];
        schedule: RangeScheduleData;
        location: RangeLocation;
        priceHighlights?: ShotPriceHighlight[];
    }>(),
    {
        cart: () => ({}),
        instructors: () => [],
        priceHighlights: () => [],
    }
);
</script>
