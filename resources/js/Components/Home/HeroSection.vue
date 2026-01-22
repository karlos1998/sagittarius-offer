<template>
    <div class="relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-500/10 to-gray-700/10"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
            <div class="text-center">
                <!-- Logo/Icon -->
                <div
                    class="mx-auto w-24 h-24 bg-gradient-to-br from-white to-gray-300 rounded-full flex items-center justify-center mb-8 shadow-2xl">
                    <svg class="w-12 h-12 text-black" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-bold text-white mb-6">
                    <span
                        class="bg-gradient-to-r from-white via-gray-200 to-gray-400 bg-clip-text text-transparent">
                        SAGGITARIUS
                    </span>
                </h1>

                <!-- Subtitle -->
                <p class="text-xl sm:text-2xl text-slate-300 mb-4 font-light">
                    Klub Sportowy
                </p>

                <!-- Location -->
                <div class="flex items-center justify-center mb-12">
                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="text-slate-300 text-lg">
                        Strzelnica Forty • Piątnica
                    </span>
                </div>

                <!-- CTA Button -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <Link
                        :href="route('guns.index')"
                        class="group relative px-8 py-4 bg-white text-black font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 overflow-hidden"
                    >
                        <span class="relative z-10">Przejdź do oferty</span>
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-gray-100 to-gray-200 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                        <div
                            class="absolute -inset-1 bg-gradient-to-r from-gray-200 to-gray-300 rounded-lg blur opacity-25 group-hover:opacity-50 transition-opacity duration-200"></div>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute top-1/4 left-10 w-20 h-20 bg-gray-500/10 rounded-full blur-xl"></div>
        <div class="absolute bottom-1/4 right-10 w-32 h-32 bg-gray-500/10 rounded-full blur-xl"></div>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
</script>
