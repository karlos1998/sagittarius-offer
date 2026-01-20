<template>
  <div
      class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-6 border border-slate-700/50 hover:border-orange-500/50 transition-all duration-300">
    <div
        class="w-12 h-12 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center mb-4">
      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
      </svg>
    </div>
    <h3 class="text-xl font-semibold text-white mb-2">{{ feature.title }}</h3>
    <p class="text-slate-400">{{ feature.description }}</p>
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
