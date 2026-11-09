<template>
    <section class="overflow-hidden bg-white pb-16 sm:pb-20">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold tracking-tight text-black sm:text-3xl">
                Tak to wygląda na miejscu
            </h2>

            <div class="-mx-4 mt-8 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-2 sm:overflow-visible sm:px-0 lg:grid-cols-4">
                <figure
                    v-for="photo in photos"
                    :key="photo.src"
                    class="w-[78%] shrink-0 snap-start sm:w-auto"
                >
                    <img
                        :src="photo.src"
                        :alt="photo.alt"
                        class="h-96 w-full rounded-3xl object-cover"
                        loading="lazy"
                    >
                    <figcaption class="mt-3 text-sm text-black/60">{{ photo.caption }}</figcaption>
                </figure>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
const photos = [
    {
        src: '/images/range/shotgun-shooter-lane.jpg',
        alt: 'Strzelec ze strzelbą na stanowisku, przed nim oś strzelnicy z tarczami',
        caption: 'Strzelba na osi',
    },
    {
        src: '/images/range/teen-shooter-rifle.jpg',
        alt: 'Nastolatek w słuchawkach celuje z karabinka',
        caption: 'Pierwsze strzały z karabinka',
    },
    {
        src: '/images/range/pistol-lane.jpg',
        alt: 'Stanowisko z pistoletem, magazynkami i amunicją, w głębi tarcza',
        caption: 'Stanowisko pistoletowe',
    },
    {
        src: '/images/range/shooter-booth.jpg',
        alt: 'Strzelec w słuchawkach na stanowisku strzeleckim',
        caption: 'Stanowiska oddzielone od siebie',
    },
];
</script>
