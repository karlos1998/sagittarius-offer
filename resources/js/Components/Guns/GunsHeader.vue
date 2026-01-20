<template>
    <div class="relative overflow-hidden bg-slate-800/50">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-red-500/10"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <Link :href="route('home')" class="inline-flex items-center text-orange-400 hover:text-orange-300 mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do strony głównej
                </Link>

                <h1 class="text-4xl sm:text-6xl font-bold text-white mb-4">
                    <span
                        class="bg-gradient-to-r from-orange-400 via-red-500 to-pink-500 bg-clip-text text-transparent">
                        NASZA OFERTA BRONI
                    </span>
                </h1>

                <p class="text-xl text-slate-300 max-w-2xl mx-auto">
                    Profesjonalna broń sportowa dostępna w Strzelnicy Forty
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
</script>
