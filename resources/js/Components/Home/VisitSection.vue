<template>
    <section class="border-t border-black/10 bg-stone-50 py-16 sm:py-20">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8">
            <div>
                <h2 class="text-3xl font-semibold tracking-tight text-black sm:text-4xl">
                    Do zobaczenia na strzelnicy
                </h2>
                <dl class="mt-8 flex flex-col gap-5 text-black">
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-black/50">Adres</dt>
                        <dd class="mt-1 text-lg">
                            {{ location.address.street }}<br>
                            {{ location.address.postal_code }} {{ location.address.city }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-black/50">Godziny</dt>
                        <dd class="mt-1 text-lg">{{ hoursSummary }}</dd>
                        <dd class="text-sm text-black/60">W święta nieczynne.</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-black/50">Płatność</dt>
                        <dd class="mt-1 text-lg">Na miejscu, gotówką lub kartą</dd>
                    </div>
                </dl>
                <a
                    :href="location.maps_url"
                    target="_blank"
                    rel="noopener"
                    class="mt-8 inline-flex items-center justify-center rounded border border-black bg-amber-300 px-6 py-3 text-sm font-semibold text-black transition-colors hover:bg-black hover:text-white"
                >
                    Wyznacz trasę w Google Maps
                </a>
            </div>

            <div class="overflow-hidden rounded-3xl border border-black/10 bg-stone-200">
                <iframe
                    title="Mapa dojazdu do strzelnicy KS Sagittarius"
                    src="https://www.google.com/maps?q=Klub+Strzelecki+SAGITTARIUS,+Stawiskowska+57A,+18-421+Pi%C4%85tnica+Poduchowna&z=14&output=embed"
                    class="h-80 w-full sm:h-[420px]"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                />
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { RangeLocation, RangeScheduleData } from '@/types/storefront';
import { formatHours } from '@/utils/schedule';

const props = defineProps<{
    location: RangeLocation;
    schedule: RangeScheduleData;
}>();

const hoursSummary = computed(() => {
    const regularDays = props.schedule.days.filter((day) => !day.closure_reason);
    const distinctHours = [...new Set(regularDays.map((day) => (day.is_open ? formatHours(day, props.schedule.timezone) : null)))];

    if (distinctHours.length === 1 && distinctHours[0] !== null) {
        return `Codziennie ${distinctHours[0]}`;
    }

    return 'Zmienne, aktualne godziny widzisz na górze strony';
});
</script>
