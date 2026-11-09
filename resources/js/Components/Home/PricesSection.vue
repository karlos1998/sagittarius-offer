<template>
    <section v-if="highlights.length" class="bg-white py-16 sm:py-20">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">
                        Ile to kosztuje
                    </p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                        Płacisz za naboje, które wystrzelisz. Nic więcej.
                    </h2>
                    <p class="mt-4 leading-7 text-black/70">
                        Bez opłat za wejście i za stanowisko. Poniżej najtańszy sposób, żeby spróbować
                        każdego rodzaju broni.
                    </p>
                </div>
                <Link
                    :href="route('guns.index')"
                    class="inline-flex w-fit items-center justify-center rounded border border-black bg-black px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-black"
                >
                    Cała broń ({{ totalGuns }} szt.) i ceny
                </Link>
            </div>

            <img
                src="/images/range/gun-wall.jpg"
                alt="Ściana z pistoletami i karabinami na podświetlanych półkach"
                class="mt-10 h-56 w-full rounded-3xl object-cover sm:h-80"
                loading="lazy"
            >

            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <article
                    v-for="highlight in highlights"
                    :key="highlight.gun_type"
                    class="flex flex-col rounded-3xl border border-black/10 bg-stone-50 p-6"
                >
                    <h3 class="text-lg font-semibold text-black">{{ highlight.gun_type }}</h3>
                    <p class="mt-4 text-sm text-black/60">{{ highlight.shots }} strzałów od</p>
                    <p class="text-4xl font-semibold tracking-tight text-black">
                        {{ formatShotsPrice(highlight.standard_price_per_shot, highlight.shots) }}
                    </p>
                    <p class="mt-1 text-sm text-black/60">
                        klubowicze od {{ formatShotsPrice(highlight.club_price_per_shot, highlight.shots) }}
                    </p>
                    <p class="mt-5 border-t border-black/10 pt-4 text-sm leading-6 text-black/70">
                        {{ highlight.example_guns.join(', ') }}<span v-if="highlight.guns_count > highlight.example_guns.length"> i inne</span>
                    </p>
                </article>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { ShotPriceHighlight } from '@/types/storefront';

const props = defineProps<{
    highlights: ShotPriceHighlight[];
}>();

const totalGuns = computed(() => props.highlights.reduce((sum, highlight) => sum + highlight.guns_count, 0));

function formatShotsPrice(pricePerShot: number, shots: number): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
        maximumFractionDigits: 2,
        minimumFractionDigits: 0,
    }).format(pricePerShot * shots);
}
</script>
