<template>
    <div class="fixed top-4 right-4 z-50">
        <Link
            :href="route('cart.index')"
            class="relative bg-white hover:bg-gray-100 text-black p-3 rounded-full shadow-2xl border-2 border-gray-300 transition-all duration-200 hover:scale-110 flex items-center justify-center"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2 8h12l-2-8M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-3"/>
            </svg>

            <!-- Counter badge -->
            <div
                v-if="itemCount > 0"
                class="absolute -top-2 -right-2 bg-gray-800 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center border-2 border-white min-w-[24px]"
            >
                {{ itemCount }}
            </div>
        </Link>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { CartMap } from '@/types/storefront';
import { countCartItems } from '@/utils/cart';

const props = withDefaults(
    defineProps<{
        cart?: CartMap;
    }>(),
    {
        cart: () => ({}),
    }
);

const itemCount = computed(() => countCartItems(props.cart));
</script>
