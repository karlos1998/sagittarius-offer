<template>
    <section id="z-dzieckiem" class="bg-black py-16 text-white sm:py-20">
        <div class="mx-auto grid w-full max-w-7xl gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-300">
                    Z dzieckiem
                </p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">
                    Nie mamy dolnej granicy wieku. Decydujesz ty.
                </h2>
                <p class="mt-5 text-lg leading-8 text-white/75">
                    Znasz swoje dziecko lepiej niż ktokolwiek z nas. Zanim przyjedziecie, odpowiedz
                    sobie szczerze na kilka pytań:
                </p>

                <ul class="mt-6 flex flex-col gap-3">
                    <li
                        v-for="question in questions"
                        :key="question"
                        class="flex gap-3 leading-7 text-white/90"
                    >
                        <span class="mt-2.5 h-1.5 w-1.5 shrink-0 rounded-full bg-amber-300" />
                        {{ question }}
                    </li>
                </ul>

                <p class="mt-6 leading-7 text-white/75">
                    Jeśli wszędzie odpowiadasz „tak”, przyjeżdżajcie. Dziecko strzela pod twoją opieką
                    i pod okiem instruktora, a ty za nie odpowiadasz. Na początek najlepiej sprawdza się
                    karabinek .22 LR: mały odrzut, niewielki huk i najtańsze strzały.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <img
                    src="/images/range/young-shooter-booth.jpg"
                    alt="Nastolatek w słuchawkach strzela z karabinka na stanowisku"
                    class="h-80 w-full rounded-3xl object-cover sm:h-[460px]"
                    loading="lazy"
                >
                <img
                    src="/images/range/rifle-bench.jpg"
                    alt="Karabinek na stole strzeleckim z amunicją małokalibrową"
                    class="mt-12 h-80 w-full rounded-3xl object-cover sm:h-[460px]"
                    loading="lazy"
                >
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const questions = [
    'Czy utrzyma karabinek albo pistolet w rękach dłużej niż chwilę?',
    'Czy słucha poleceń dorosłych i robi dokładnie to, co mu się powie, a nie po swojemu?',
    'Czy wytrzyma huk i słuchawki na uszach?',
    'Czy nie zacznie się wygłupiać, kiedy będzie trzymać prawdziwą broń?',
];
</script>
