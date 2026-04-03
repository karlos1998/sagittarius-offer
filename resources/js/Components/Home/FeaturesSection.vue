<template>
  <div class="py-12">
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="mb-8 text-center">
        <h2 class="text-2xl font-semibold">Dlaczego Sagittarius</h2>
      </div>
      <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
        <FeatureCard
            v-for="(feature, index) in features"
            :key="index"
            :feature="feature"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import FeatureCard from './FeatureCard.vue';

const features = [
  {
    icon: 'M13 10V3L4 14h7v7l9-11h-7z',
    title: 'Profesjonalne wyposażenie',
    description: 'Najnowocześniejsza strzelnica z profesjonalnym sprzętem i instruktorami.'
  },
  {
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    title: 'Doświadczeni instruktorzy',
    description: 'Nasi certyfikowani instruktorzy zapewnią bezpieczeństwo i profesjonalne szkolenie.'
  },
  {
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    title: 'Bezpieczeństwo przede wszystkim',
    description: 'Najwyższe standardy bezpieczeństwa i przestrzeganie wszystkich przepisów.'
  }
];
</script>
