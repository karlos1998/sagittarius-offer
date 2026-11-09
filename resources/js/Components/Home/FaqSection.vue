<template>
    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">
                    Pytania
                </p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                    O to ludzie pytają nas najczęściej
                </h2>
            </div>

            <div class="divide-y divide-black/10 border-y border-black/10">
                <details
                    v-for="item in items"
                    :key="item.question"
                    class="group py-5"
                >
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-left text-lg font-semibold text-black [&::-webkit-details-marker]:hidden">
                        {{ item.question }}
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-black/20 text-xl leading-none transition-transform group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-7 text-black/70">
                        {{ item.answer }}
                        <a
                            v-if="item.link"
                            :href="item.link.routeName ? route(item.link.routeName) : item.link.href"
                            :target="item.link.external ? '_blank' : undefined"
                            :rel="item.link.external ? 'noopener' : undefined"
                            class="font-semibold text-black underline decoration-amber-400 decoration-2 underline-offset-4"
                        >{{ item.link.label }}</a>
                    </p>
                </details>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    permitHelpUrl: string;
}>();

interface FaqItem {
    question: string;
    answer: string;
    link?: { href?: string; routeName?: string; label: string; external?: boolean };
}

const items = computed<FaqItem[]>(() => [
    {
        question: 'Czy muszę się wcześniej zapisać albo zadzwonić?',
        answer: 'Nie. Przyjeżdżasz w godzinach otwarcia i strzelasz. Miejsce się znajdzie.',
    },
    {
        question: 'Czy mogę przyjechać dzisiaj albo jutro?',
        answer: 'Na górze tej strony zawsze widać, czy dziś jest otwarte, do której i jak wyglądają najbliższe dni, łącznie ze świętami.',
        link: { href: '#top', label: 'Sprawdź godziny' },
    },
    {
        question: 'Czy potrzebuję pozwolenia na broń albo patentu?',
        answer: 'Nie. Bez uprawnień strzelasz razem z instruktorem. Z patentem lub pozwoleniem możesz strzelać sam.',
    },
    {
        question: 'Mogę przyjechać z 12-letnim dzieckiem?',
        answer: 'Tak, jeśli uważasz, że sobie poradzi. Nie mamy sztywnej granicy wieku, decyzja i odpowiedzialność są po stronie rodzica.',
        link: { href: '#z-dzieckiem', label: 'Na co zwrócić uwagę' },
    },
    {
        question: 'Ile to kosztuje?',
        answer: 'Płacisz tylko za wystrzelone naboje. Cena zależy od kalibru, a klubowicze mają niższe stawki. Dokładne ceny są przy każdej broni.',
        link: { routeName: 'guns.index', label: 'Zobacz cennik' },
    },
    {
        question: 'Mogę zapłacić kartą?',
        answer: 'Tak, kartą albo gotówką, na miejscu.',
    },
    {
        question: 'Co zabrać ze sobą?',
        answer: 'Dowód osobisty. Broń, amunicję, słuchawki i okulary masz na miejscu.',
    },
    {
        question: 'Chcę zrobić pozwolenie na broń. Od czego zacząć?',
        answer: 'Od przyjazdu i zapisu do klubu. Potem szkolenie, egzamin na patent i wniosek do WPA. Wszystko krok po kroku opisaliśmy tutaj:',
        link: { href: props.permitHelpUrl, label: 'pomoc.kssagittarius.pl', external: true },
    },
]);
</script>
