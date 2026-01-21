<template>
    <div
        class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border-2 border-red-600/30 hover:border-red-500/60 transition-all duration-300 hover:transform hover:scale-105 shadow-2xl">
        <div
            class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-700 rounded-lg flex items-center justify-center mb-4 border border-red-500/50 shadow-lg">
      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
      </svg>
    </div>
      <h3 class="text-xl font-bold text-white mb-2">{{ feature.title }}</h3>
      <p class="text-green-100">{{ feature.description }}</p>
  </div>
</template>

<script setup>
defineProps({
  feature: {
    type: Object,
    required: true
  }
});
</script>
