<template>
    <div
        class="rounded border border-black/30 bg-white p-6 transition-colors hover:border-black">
        <div
            class="mb-4 flex h-12 w-12 items-center justify-center rounded border border-black/30">
            <svg class="h-6 w-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon"/>
      </svg>
    </div>
      <h3 class="mb-2 text-xl font-semibold">{{ feature.title }}</h3>
        <p class="text-black/70">{{ feature.description }}</p>
  </div>
</template>

<script setup lang="ts">
interface Feature {
    title: string;
    description: string;
    icon: string;
}

defineProps<{
    feature: Feature;
}>();
</script>
