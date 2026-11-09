<template>
    <section class="border-y border-black/10 bg-stone-50 py-16 sm:py-20">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">
                    Dla kogo
                </p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                    Nieważne, czy strzelasz od lat, czy to twój pierwszy raz
                </h2>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-5 md:grid-cols-3">
                <article class="flex flex-col rounded-3xl border border-black/10 bg-white p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/50">Bez uprawnień</p>
                    <h3 class="mt-3 text-xl font-semibold text-black">Pierwszy raz z bronią</h3>
                    <p class="mt-3 flex-1 leading-7 text-black/70">
                        Nie potrzebujesz pozwolenia, patentu ani doświadczenia. Strzelasz razem z jednym
                        z naszych instruktorów. Pokaże, poprawi i przypilnuje, żeby było bezpiecznie.
                    </p>
                    <a href="#instruktorzy" class="mt-6 text-sm font-semibold text-black underline decoration-amber-400 decoration-2 underline-offset-4 hover:decoration-black">
                        Zobacz, kto będzie obok ciebie
                    </a>
                </article>

                <article class="flex flex-col rounded-3xl border border-black/10 bg-white p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/50">Z patentem lub pozwoleniem</p>
                    <h3 class="mt-3 text-xl font-semibold text-black">Strzelasz sam</h3>
                    <p class="mt-3 flex-1 leading-7 text-black/70">
                        Bierzesz broń z naszej listy i strzelasz we własnym tempie. Klubowicze płacą
                        za amunicję mniej, ceny klubowe są przy każdej broni.
                    </p>
                    <Link :href="route('guns.index')" class="mt-6 text-sm font-semibold text-black underline decoration-amber-400 decoration-2 underline-offset-4 hover:decoration-black">
                        Broń i ceny amunicji
                    </Link>
                </article>

                <article class="flex flex-col rounded-3xl border border-black bg-black p-7 text-white">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-300">Własne pozwolenie</p>
                    <h3 class="mt-3 text-xl font-semibold">Chcesz mieć swoją broń</h3>
                    <p class="mt-3 flex-1 leading-7 text-white/75">
                        Wszystko zrobisz u nas: zapis do klubu, szkolenie, egzamin na patent strzelecki
                        PZSS i dokumenty do pozwolenia na broń sportową. Przeprowadzimy cię przez to krok po kroku.
                    </p>
                    <a
                        :href="permitHelpUrl"
                        target="_blank"
                        rel="noopener"
                        class="mt-6 text-sm font-semibold text-white underline decoration-amber-300 decoration-2 underline-offset-4 hover:decoration-white"
                    >
                        Jak to wygląda krok po kroku
                    </a>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    permitHelpUrl: string;
}>();
</script>
