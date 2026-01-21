<template>
    <div class="relative overflow-hidden bg-gradient-to-br from-gray-900 to-black">
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-gradient-to-br from-red-900/30 to-yellow-900/20"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <Link :href="route('home')"
                      class="inline-flex items-center text-red-400 hover:text-red-300 mb-4 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Powrót do strony głównej
                </Link>

                <h1 class="text-4xl sm:text-6xl font-bold text-white mb-4">
                    <span
                        class="bg-gradient-to-r from-red-500 via-yellow-400 to-red-600 bg-clip-text text-transparent drop-shadow-lg">
                        NASZA OFERTA BRONI
                    </span>
                </h1>

                <p class="text-xl text-green-100 max-w-2xl mx-auto">
                    Profesjonalna broń sportowa dostępna w Strzelnicy Forty
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
</script>
