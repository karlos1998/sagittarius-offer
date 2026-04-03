<template>
    <div class="border-b border-black/20 bg-white py-8">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <Link :href="route('home')" class="inline-flex items-center text-sm text-black/70 hover:text-black">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Powrót do strony głównej
                </Link>

                <h1 class="mt-3 inline-flex items-center gap-2 text-3xl font-semibold sm:text-4xl">
                    <span class="inline-block h-2 w-2 rounded-full bg-amber-300" />
                    Nasza oferta broni
                </h1>

                <p class="mx-auto mt-2 max-w-2xl text-sm text-black/60 sm:text-base">
                    Profesjonalna broń sportowa dostępna w Strzelnicy Forty.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>
