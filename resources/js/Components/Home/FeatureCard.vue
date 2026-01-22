<template>
    <div
        class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl p-6 border-2 border-gray-600/30 hover:border-gray-500/60 transition-all duration-300 hover:transform hover:scale-105 shadow-2xl">
        <div
            class="w-12 h-12 bg-gradient-to-br from-white to-gray-200 rounded-lg flex items-center justify-center mb-4 border border-gray-300/50 shadow-lg">
            <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
      </svg>
    </div>
      <h3 class="text-xl font-bold text-white mb-2">{{ feature.title }}</h3>
        <p class="text-gray-300">{{ feature.description }}</p>
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
