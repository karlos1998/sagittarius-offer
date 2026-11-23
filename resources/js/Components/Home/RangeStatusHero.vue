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
                    {{ introText }}
                </p>

                <div
                    v-if="showCallAhead"
                    class="mt-6 flex max-w-xl flex-col gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <p class="text-sm leading-6 text-amber-950">
                        <span class="font-semibold">W święta zadzwoń przed przyjazdem.</span>
                        Odbiera {{ location.contact.name }}.
                    </p>
                    <a
                        :href="`tel:${location.contact.phone}`"
                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded border border-black bg-black px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-white hover:text-black"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5.5A2.5 2.5 0 0 1 5.5 3h1.6a1 1 0 0 1 .95.68l1.2 3.6a1 1 0 0 1-.5 1.2l-1.8.9a11 11 0 0 0 5.67 5.67l.9-1.8a1 1 0 0 1 1.2-.5l3.6 1.2a1 1 0 0 1 .68.95v1.6A2.5 2.5 0 0 1 18.5 21C10.5 21 3 13.5 3 5.5Z" />
                        </svg>
                        {{ location.contact.phone_display }}
                    </a>
                </div>

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
                                    : day.call_ahead_reason
                                        ? 'border-dashed border-amber-400 bg-amber-50 text-amber-950'
                                        : 'border-dashed border-black/20 bg-stone-50 text-black/50'"
                            :title="day.closure_reason ?? day.call_ahead_reason ?? undefined"
                        >
                            <span class="text-xs font-semibold uppercase">
                                {{ dayLabel(day, todayDate, tomorrowDate) }}
                            </span>
                            <span class="text-[11px] opacity-60">{{ shortDate(day.date) }}</span>
                            <span v-if="day.is_open" class="mt-1 text-xs font-medium leading-4">
                                {{ formatTime(day.opens_at, schedule.timezone) }}<br>
                                <span class="opacity-70">do {{ formatTime(day.closes_at, schedule.timezone) }}</span>
                            </span>
                            <span v-else-if="day.call_ahead_reason" class="mt-1 text-xs font-medium leading-4">
                                święto<br>
                                <span class="opacity-70">zadzwoń</span>
                            </span>
                            <span v-else class="mt-1 text-xs font-medium leading-4">nieczynne</span>
                        </li>
                    </ol>
                    <p
                        v-if="specialDays.length"
                        class="mt-3 text-xs text-black/50"
                    >
                        <span v-for="(day, index) in specialDays" :key="day.date">
                            {{ index ? ' · ' : '' }}{{ shortDate(day.date) }}
                            {{ day.call_ahead_reason ? `${day.call_ahead_reason}, zadzwoń` : `nieczynne: ${day.closure_reason}` }}
                        </span>
                    </p>
                </div>
            </div>

            <RangeVideo />
        </div>
    </section>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import RangeVideo from './RangeVideo.vue';
import type { RangeDay, RangeLocation, RangeScheduleData } from '@/types/storefront';
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
const specialDays = computed(() => weekDays.value.filter((day) => day.closure_reason || day.call_ahead_reason));
const showCallAhead = computed(() => weekDays.value.some((day) => day.call_ahead_reason));

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

const upcomingClosures = computed(() => {
    const nextOpenDate = status.value.nextOpenDay?.date ?? null;

    return props.schedule.days.filter((day) => day.date >= todayDate.value
        && (nextOpenDate === null || day.date < nextOpenDate)
        && !day.is_open
        && day.closure_reason);
});

function dayNameFor(day: RangeDay): string {
    if (day.date === todayDate.value) {
        return 'dziś';
    }

    return relativeDayPhrase(day, tomorrowDate.value);
}

function capitalize(text: string): string {
    return `${text.charAt(0).toUpperCase()}${text.slice(1)}`;
}

function joinPl(parts: string[]): string {
    return parts.length > 1 ? `${parts.slice(0, -1).join(', ')} i ${parts[parts.length - 1]}` : (parts[0] ?? '');
}

const upcomingCallAheadDays = computed(() => {
    const nextOpenDate = status.value.nextOpenDay?.date ?? null;

    return props.schedule.days.filter((day) => day.date > todayDate.value
        && (nextOpenDate === null || day.date < nextOpenDate)
        && day.call_ahead_reason);
});

const callAheadNote = computed(() => {
    const days = upcomingCallAheadDays.value;

    if (!days.length) {
        return null;
    }

    if (days.length === 1) {
        return `${capitalize(dayNameFor(days[0]))} święto: ${days[0].call_ahead_reason}. Często wtedy strzelamy, tylko w innych godzinach, więc zadzwoń.`;
    }

    const parts = days.map((day) => `${dayNameFor(day)} (${day.call_ahead_reason})`);

    return `Święta: ${joinPl(parts)}. Często wtedy strzelamy, tylko w innych godzinach, więc zadzwoń.`;
});

const closureNote = computed(() => {
    const closures = upcomingClosures.value;

    if (!closures.length) {
        return null;
    }

    const label = (date: string, weekdayPhrase: string): string => {
        if (date === todayDate.value) {
            return 'dziś';
        }

        return date === tomorrowDate.value ? 'jutro' : weekdayPhrase;
    };

    const labelled = closures.map((day) => ({
        label: label(day.date, relativeDayPhrase(day, tomorrowDate.value)),
        reason: day.closure_reason,
    }));

    if (labelled.length === 1) {
        const [{ label: dayName, reason }] = labelled;

        return `${dayName.charAt(0).toUpperCase()}${dayName.slice(1)} nieczynne: ${reason}.`;
    }

    const parts = labelled.map(({ label: dayName, reason }) => `${dayName} (${reason})`);

    return `Nieczynne: ${parts.slice(0, -1).join(', ')} i ${parts[parts.length - 1]}.`;
});

const closeTime = computed(() => formatTime(status.value.today?.closes_at ?? null, props.schedule.timezone));
const openTime = computed(() => formatTime(status.value.today?.opens_at ?? null, props.schedule.timezone));

const badgeText = computed(() => {
    if (status.value.state === 'open') {
        return isClosingSoon(status.value) ? `Otwarte jeszcze chwilę, do ${closeTime.value}` : `Otwarte teraz, do ${closeTime.value}`;
    }

    if (status.value.state === 'opens_later_today') {
        return `Dziś otwieramy o ${openTime.value}`;
    }

    if (status.value.state === 'call_ahead_today') {
        return `Dziś święto: ${status.value.today?.call_ahead_reason}`;
    }

    if (nextOpenDayName.value) {
        return `Otwieramy ${nextOpenDayName.value} o ${nextOpenTime.value}`;
    }

    return 'Chwilowo nieczynne';
});

const headline = computed(() => {
    if (status.value.state === 'open') {
        return isClosingSoon(status.value)
            ? `Zdążysz jeszcze dziś. A jak nie, wpadaj ${nextOpenDayName.value ?? 'innym razem'}.`
            : `Wpadaj dziś. Strzelamy do ${closeTime.value}.`;
    }

    if (status.value.state === 'opens_later_today') {
        return `Dziś strzelamy od ${openTime.value} do ${closeTime.value}. Przyjedź.`;
    }

    if (status.value.state === 'call_ahead_today') {
        return 'Dziś święto. Zadzwoń, a powiemy ci, do której strzelamy.';
    }

    if (nextOpenDayName.value) {
        return `Wpadnij ${nextOpenDayName.value}. Strzelamy od ${nextOpenTime.value} do ${nextCloseTime.value}.`;
    }

    return 'Strzelnica jest chwilowo nieczynna.';
});

const countdownText = computed(() => {
    if (status.value.state === 'open') {
        const left = `Do zamknięcia zostało ${formatDuration(status.value.minutesUntilClose)}.`;

        const notes = [callAheadNote.value, closureNote.value].filter(Boolean).join(' ');

        if (isClosingSoon(status.value) && nextOpenPhrase.value) {
            return notes
                ? `${left} ${notes} ${callAheadNote.value ? 'Bez dzwonienia jesteśmy' : 'Jesteśmy'} ${nextOpenPhrase.value}.`
                : `${left} Jesteśmy też ${nextOpenPhrase.value}.`;
        }

        return notes ? `${left} ${notes}` : left;
    }

    if (status.value.state === 'opens_later_today') {
        return `Otwieramy za ${formatDuration(status.value.minutesUntilOpen)}.`;
    }

    if (status.value.state === 'call_ahead_today') {
        const regularDay = nextOpenPhrase.value ? ` Bez dzwonienia wpadniesz ${nextOpenPhrase.value}.` : '';

        return `W święta często jesteśmy otwarci, tylko w innych godzinach.${regularDay}`;
    }

    return [callAheadNote.value, closureNote.value].filter(Boolean).join(' ') || null;
});

const introText = computed(() => {
    if (status.value.state === 'call_ahead_today') {
        return 'Na co dzień nie trzeba dzwonić ani się zapisywać. Przyjeżdżasz, dostajesz broń i słuchawki, a instruktor stoi obok i wszystko ci pokazuje. Pozwolenie nie jest potrzebne.';
    }

    return 'Nie musisz dzwonić ani się zapisywać. Przyjeżdżasz, dostajesz broń i słuchawki, a instruktor stoi obok i wszystko ci pokazuje. Pozwolenie nie jest potrzebne.';
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
