<template>
    <section id="top" class="border-b border-black/10 bg-white">
        <div class="mx-auto grid w-full max-w-7xl gap-10 px-4 pb-14 pt-10 sm:px-6 lg:grid-cols-[1.15fr_0.85fr] lg:gap-14 lg:px-8 lg:pb-20 lg:pt-16">
            <div class="flex flex-col">
                <div
                    class="inline-flex w-fit items-center gap-2.5 rounded-full border px-4 py-2 text-sm font-semibold"
                    :class="badgeClass"
                >
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            v-if="status.state === 'open'"
                            class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-60"
                        />
                        <span class="relative inline-flex h-2.5 w-2.5 rounded-full" :class="dotClass" />
                    </span>
                    {{ badgeText }}
                </div>

                <h1 class="mt-6 text-4xl font-semibold leading-[1.05] tracking-tight text-black sm:text-6xl">
                    {{ headline }}
                </h1>

                <p
                    v-if="countdownText"
                    class="mt-5 text-lg font-medium text-black sm:text-xl"
                >
                    {{ countdownText }}
                </p>

                <p class="mt-5 max-w-xl text-lg leading-8 text-black/70">
                    Nie musisz dzwonić ani się zapisywać. Przyjeżdżasz, dostajesz broń i słuchawki,
                    a instruktor stoi obok i wszystko ci pokazuje. Pozwolenie nie jest potrzebne.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a
                        :href="location.maps_url"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex items-center justify-center gap-2 rounded border border-black bg-amber-300 px-6 py-3 text-sm font-semibold text-black transition-colors hover:bg-black hover:text-white"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-7.75-7-12a7 7 0 1 1 14 0c0 4.25-7 12-7 12Z" />
                            <circle cx="12" cy="9" r="2.5" />
                        </svg>
                        Prowadź na strzelnicę
                    </a>
                    <Link
                        :href="route('guns.index')"
                        class="inline-flex items-center justify-center rounded border border-black bg-white px-6 py-3 text-sm font-semibold text-black transition-colors hover:bg-black hover:text-white"
                    >
                        Z czego postrzelasz i za ile
                    </Link>
                </div>

                <p class="mt-4 text-sm text-black/60">
                    {{ location.address.street }}, {{ location.address.postal_code }} {{ location.address.city }}
                    · płacisz na miejscu, także kartą
                </p>

                <div class="mt-10">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-black/50">
                        Najbliższe dni
                    </p>
                    <ol class="mt-3 grid grid-cols-4 gap-2 sm:grid-cols-7">
                        <li
                            v-for="day in weekDays"
                            :key="day.date"
                            class="flex flex-col rounded-xl border px-2 py-2.5 text-center"
                            :class="day.date === todayDate
                                ? 'border-black bg-black text-white'
                                : day.is_open
                                    ? 'border-black/15 bg-white text-black'
                                    : 'border-dashed border-black/20 bg-stone-50 text-black/50'"
                            :title="day.closure_reason ?? undefined"
                        >
                            <span class="text-xs font-semibold uppercase">
                                {{ dayLabel(day, todayDate, tomorrowDate) }}
                            </span>
                            <span class="text-[11px] opacity-60">{{ shortDate(day.date) }}</span>
                            <span v-if="day.is_open" class="mt-1 text-xs font-medium leading-4">
                                {{ formatTime(day.opens_at, schedule.timezone) }}<br>
                                <span class="opacity-70">do {{ formatTime(day.closes_at, schedule.timezone) }}</span>
                            </span>
                            <span v-else class="mt-1 text-xs font-medium leading-4">nieczynne</span>
                        </li>
                    </ol>
                    <p
                        v-if="closedWithReason.length"
                        class="mt-3 text-xs text-black/50"
                    >
                        <span v-for="(day, index) in closedWithReason" :key="day.date">
                            {{ index ? ' · ' : '' }}{{ shortDate(day.date) }} nieczynne: {{ day.closure_reason }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="relative hidden min-h-[520px] overflow-hidden rounded-3xl bg-stone-200 lg:block">
                <img
                    src="/images/range/teen-shooter-rifle.jpg"
                    alt="Młody strzelec z karabinkiem na stanowisku strzelnicy Sagittarius"
                    class="absolute inset-0 h-full w-full object-cover"
                >
                <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-6 pt-24 text-white">
                    <p class="text-sm font-semibold">Strzelnica Forty · Piątnica</p>
                    <p class="mt-1 text-sm text-white/80">Pistolety, karabiny, strzelby i rewolwery na miejscu.</p>
                </div>
            </div>
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import type { RangeLocation, RangeScheduleData } from '@/types/storefront';
import {
    addDaysToDateString,
    dayLabel,
    describeRangeStatus,
    formatDuration,
    formatTime,
    isClosingSoon,
    localDateString,
    relativeDayPhrase,
    shortDate,
} from '@/utils/schedule';

const props = defineProps<{
    schedule: RangeScheduleData;
    location: RangeLocation;
}>();

const CLOCK_TICK_MS = 30_000;

const now = ref(Date.parse(props.schedule.generated_at));
let clock: ReturnType<typeof setInterval> | undefined;

onMounted(() => {
    now.value = Date.now();
    clock = setInterval(() => {
        now.value = Date.now();
    }, CLOCK_TICK_MS);
});

onBeforeUnmount(() => {
    if (clock) {
        clearInterval(clock);
    }
});

const status = computed(() => describeRangeStatus(props.schedule.days, now.value, props.schedule.timezone));
const todayDate = computed(() => localDateString(now.value, props.schedule.timezone));
const tomorrowDate = computed(() => addDaysToDateString(todayDate.value, 1));

const weekDays = computed(() => props.schedule.days.filter((day) => day.date >= todayDate.value).slice(0, 7));
const closedWithReason = computed(() => weekDays.value.filter((day) => !day.is_open && day.closure_reason));

const nextOpenPhrase = computed(() => {
    const day = status.value.nextOpenDay;

    if (!day) {
        return null;
    }

    return `${relativeDayPhrase(day, tomorrowDate.value)} od ${formatTime(day.opens_at, props.schedule.timezone)}`;
});

const nextOpenDayName = computed(() => {
    const day = status.value.nextOpenDay;

    return day ? relativeDayPhrase(day, tomorrowDate.value) : null;
});

const nextOpenTime = computed(() => formatTime(status.value.nextOpenDay?.opens_at ?? null, props.schedule.timezone));
const nextCloseTime = computed(() => formatTime(status.value.nextOpenDay?.closes_at ?? null, props.schedule.timezone));

const closeTime = computed(() => formatTime(status.value.today?.closes_at ?? null, props.schedule.timezone));
const openTime = computed(() => formatTime(status.value.today?.opens_at ?? null, props.schedule.timezone));

const badgeText = computed(() => {
    if (status.value.state === 'open') {
        return isClosingSoon(status.value) ? `Otwarte jeszcze chwilę, do ${closeTime.value}` : `Otwarte teraz, do ${closeTime.value}`;
    }

    if (status.value.state === 'opens_later_today') {
        return `Dziś otwieramy o ${openTime.value}`;
    }

    if (nextOpenDayName.value) {
        return `Otwieramy ${nextOpenDayName.value} o ${nextOpenTime.value}`;
    }

    return 'Chwilowo nieczynne';
});

const headline = computed(() => {
    if (status.value.state === 'open') {
        return isClosingSoon(status.value)
            ? 'Zdążysz jeszcze dziś. A jak nie, wpadaj jutro.'
            : `Wpadaj dziś. Strzelamy do ${closeTime.value}.`;
    }

    if (status.value.state === 'opens_later_today') {
        return `Dziś strzelamy od ${openTime.value} do ${closeTime.value}. Przyjedź.`;
    }

    if (nextOpenDayName.value) {
        return `Wpadnij ${nextOpenDayName.value}. Strzelamy od ${nextOpenTime.value} do ${nextCloseTime.value}.`;
    }

    return 'Strzelnica jest chwilowo nieczynna.';
});

const countdownText = computed(() => {
    if (status.value.state === 'open') {
        const left = `Do zamknięcia zostało ${formatDuration(status.value.minutesUntilClose)}.`;

        return isClosingSoon(status.value) && nextOpenPhrase.value
            ? `${left} Jesteśmy też ${nextOpenPhrase.value}.`
            : left;
    }

    if (status.value.state === 'opens_later_today') {
        return `Otwieramy za ${formatDuration(status.value.minutesUntilOpen)}.`;
    }

    if (status.value.today?.closure_reason) {
        return `Dziś nieczynne: ${status.value.today.closure_reason}.`;
    }

    return null;
});

const badgeClass = computed(() => {
    if (status.value.state === 'open') {
        return isClosingSoon(status.value)
            ? 'border-amber-300 bg-amber-50 text-amber-900'
            : 'border-emerald-300 bg-emerald-50 text-emerald-900';
    }

    return 'border-amber-300 bg-amber-50 text-amber-900';
});

const dotClass = computed(() => {
    if (status.value.state === 'open') {
        return isClosingSoon(status.value) ? 'bg-amber-500' : 'bg-emerald-500';
    }

    return 'bg-amber-500';
});
</script>
