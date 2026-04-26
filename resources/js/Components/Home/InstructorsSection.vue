<template>
    <section class="border-t border-black/10 bg-stone-50 py-16">
        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-amber-700">
                    Zespół Sagittarius
                </p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                    Poznaj naszych instruktorów
                </h2>
                <p class="mt-4 text-base leading-7 text-black/70">
                    Doświadczeni instruktorzy dbają o bezpieczeństwo, komfort i dobrą atmosferę podczas każdej wizyty na strzelnicy.
                </p>
            </div>

            <div
                v-if="instructors.length"
                class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3"
            >
                <article
                    v-for="instructor in instructors"
                    :key="instructor.id"
                    class="overflow-hidden rounded-3xl border border-black/10 bg-white shadow-sm transition-transform duration-200 hover:-translate-y-1"
                >
                    <div class="aspect-square overflow-hidden bg-stone-200">
                        <img
                            v-if="instructor.photo_url"
                            :src="instructor.photo_url"
                            :alt="instructor.full_name"
                            class="h-full w-full object-cover"
                        >
                        <div
                            v-else
                            class="flex h-full items-center justify-center bg-gradient-to-br from-stone-200 to-stone-300 text-4xl font-semibold text-black/40"
                        >
                            {{ instructor.initials }}
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 p-6">
                        <div>
                            <h3 class="text-xl font-semibold text-black">
                                {{ instructor.full_name }}
                            </h3>
                        </div>

                        <p class="text-sm leading-6 text-black/70">
                            {{ instructor.description || 'Instruktor z doświadczeniem praktycznym i nastawieniem na bezpieczne szkolenie.' }}
                        </p>
                    </div>
                </article>
            </div>

            <div
                v-else
                class="mx-auto mt-12 max-w-3xl rounded-3xl border border-dashed border-black/15 bg-white px-6 py-10 text-center text-sm text-black/60"
            >
                Wkrótce pokażemy tutaj nasz zespół instruktorów.
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Instructor } from '@/types/storefront';

const props = defineProps<{
    instructors: Instructor[];
}>();

const instructors = computed(() =>
    props.instructors.map((instructor) => ({
        ...instructor,
        initials: instructor.full_name
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map((part) => part.charAt(0).toUpperCase())
            .join(''),
    }))
);
</script>
