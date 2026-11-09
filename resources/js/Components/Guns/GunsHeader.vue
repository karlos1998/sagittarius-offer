<template>
    <header class="border-b border-black/10 bg-white">
        <div class="mx-auto w-full max-w-7xl px-4 pb-10 pt-6 sm:px-6 lg:px-8 lg:pb-12">
            <Link
                :href="route('home')"
                class="inline-flex items-center gap-2 text-sm text-black/60 transition-colors hover:text-black"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Strona główna
            </Link>

            <div class="mt-6 grid gap-8 lg:grid-cols-[1.3fr_1fr] lg:items-end">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">
                        Oferta
                    </p>
                    <h1 class="mt-3 text-4xl font-semibold tracking-tight text-black sm:text-5xl">
                        Z czego postrzelasz i za ile
                    </h1>
                    <p class="mt-4 max-w-2xl text-lg leading-8 text-black/70">
                        Przy każdej broni widzisz cenę za strzały. Możesz po prostu przyjechać i zapłacić
                        na miejscu albo złożyć tu zamówienie wcześniej i pokazać je na strzelnicy.
                    </p>
                </div>

                <ul class="grid gap-3 text-sm sm:grid-cols-3 lg:grid-cols-1">
                    <li
                        v-for="fact in facts"
                        :key="fact.title"
                        class="flex gap-3 rounded-2xl border border-black/10 bg-stone-50 px-4 py-3"
                    >
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-amber-400" />
                        <span>
                            <span class="font-semibold text-black">{{ fact.title }}</span>
                            <span class="block text-black/60">{{ fact.text }}</span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>
    </header>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

const facts = [
    { title: 'Bez pozwolenia', text: 'Strzelasz razem z instruktorem.' },
    { title: 'Płacisz za strzały', text: 'Na miejscu, gotówką albo kartą.' },
    { title: 'Klubowicze płacą mniej', text: 'Przełącz ceny poniżej.' },
];
</script>
