<template>
    <nav class="sticky top-0 z-50 h-12 border-b border-black bg-black text-white">
        <div class="mx-auto flex h-full w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <Link :href="route('home')" class="flex items-center gap-2">
                <img src="/images/branding/kss-mark.png" alt="KS Sagittarius" class="h-7 w-7 rounded-sm border border-white/40 bg-white object-contain p-0.5">
                <span class="hidden text-xs font-semibold tracking-wide uppercase sm:inline">KS Sagittarius</span>
            </Link>

            <div class="flex items-center gap-2 sm:gap-3 text-sm">
                <Link
                    :href="route('home')"
                    :class="linkClass(route().current('home'))"
                >
                    Strona główna
                </Link>
                <Link
                    :href="route('guns.index')"
                    :class="linkClass(route().current('guns.index'))"
                >
                    Oferta
                </Link>
                <Link
                    :href="route('cart.index')"
                    :class="linkClass(route().current('cart.index') || route().current('checkout.index'))"
                >
                    Koszyk
                    <span
                        v-if="itemCount > 0"
                        class="ml-1 inline-flex min-w-5 items-center justify-center rounded-full bg-amber-300 px-1.5 py-0.5 text-[10px] font-semibold text-black"
                    >
                        {{ itemCount }}
                    </span>
                </Link>
            </div>
        </div>
    </nav>
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

function linkClass(isActive: boolean): string[] {
    return [
        'rounded border px-2 py-1 transition-colors',
        isActive
            ? 'border-amber-300 bg-amber-300 text-black'
            : 'border-white/30 text-white hover:border-white hover:bg-white hover:text-black',
    ];
}
</script>
