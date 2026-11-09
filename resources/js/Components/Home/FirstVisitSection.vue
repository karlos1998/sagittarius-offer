<template>
    <section class="bg-white py-16 sm:py-20">
        <div class="mx-auto grid w-full max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-[0.8fr_1.2fr] lg:items-center lg:px-8">
            <div class="overflow-hidden rounded-3xl bg-stone-200">
                <img
                    src="/images/range/pistol-lane.jpg"
                    alt="Stanowisko pistoletowe z magazynkami i amunicją, w tle tarcza"
                    class="h-72 w-full object-cover sm:h-96 lg:h-[560px]"
                    loading="lazy"
                >
            </div>

            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">
                    Pierwszy raz?
                </p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                    Tak wygląda wizyta od wejścia do ostatniego strzału
                </h2>

                <ol class="mt-10 flex flex-col gap-8">
                    <li
                        v-for="(step, index) in steps"
                        :key="step.title"
                        class="grid grid-cols-[3rem_1fr] gap-4"
                    >
                        <span class="flex h-12 w-12 items-center justify-center rounded-full border border-black text-lg font-semibold">
                            {{ index + 1 }}
                        </span>
                        <div>
                            <h3 class="text-lg font-semibold text-black">{{ step.title }}</h3>
                            <p class="mt-1 leading-7 text-black/70">{{ step.text }}</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const steps = [
    {
        title: 'Przyjeżdżasz, kiedy ci pasuje',
        text: 'W godzinach otwarcia, bez rezerwacji i bez telefonu. Weź ze sobą dowód osobisty.',
    },
    {
        title: 'Wybierasz, z czego chcesz strzelać',
        text: 'Pistolet, karabin, strzelba albo rewolwer. Nie wiesz, co wybrać? Powiedz instruktorowi, co chcesz sprawdzić, a on coś zaproponuje.',
    },
    {
        title: 'Instruktor pokazuje, jak to działa',
        text: 'Jak trzymać broń, jak ją załadować, jak celować i co robić, gdy coś się zatnie. Słuchawki i okulary dostajesz na miejscu.',
    },
    {
        title: 'Strzelasz',
        text: 'Instruktor stoi obok przez cały czas. Na koniec płacisz za wystrzelone naboje, gotówką albo kartą.',
    },
];
</script>
