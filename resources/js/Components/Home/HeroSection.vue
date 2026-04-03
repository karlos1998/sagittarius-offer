<template>
    <div class="border-b border-black/20 bg-white">
        <div class="mx-auto max-w-7xl px-4 pb-16 pt-16 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="mx-auto mb-8 inline-flex items-center rounded border border-black/20 px-3 py-1 text-xs font-medium uppercase tracking-wide text-black/70">
                    <span class="mr-2 inline-block h-2 w-2 rounded-full bg-amber-300" />
                    Klub Strzelecki
                </div>

                <img src="/images/branding/kss-logo.png" alt="KS Sagittarius" class="mx-auto mb-5 h-auto w-full max-w-xl">

                <p class="mb-4 text-xl sm:text-2xl text-black/70">
                    Klub Sportowy Sagittarius
                </p>

                <div class="mb-12 flex items-center justify-center">
                    <svg class="mr-2 h-5 w-5 text-black/60" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="text-lg text-black/70">
                        Strzelnica Forty • Piątnica
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link
                        :href="route('guns.index')"
                        class="rounded border border-black bg-black px-8 py-3 text-sm font-medium text-white transition-colors hover:bg-white hover:text-black"
                    >
                        Przejdź do oferty
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {Link} from '@inertiajs/vue3';
</script>
