import type { RangeDay } from '@/types/storefront';

export type RangeState = 'open' | 'opens_later_today' | 'closed_today';

export interface RangeStatus {
    state: RangeState;
    today: RangeDay | null;
    nextOpenDay: RangeDay | null;
    minutesUntilClose: number;
    minutesUntilOpen: number;
}

const CLOSING_SOON_MINUTES = 60;

const WEEKDAY_SHORT = ['', 'pon', 'wt', 'śr', 'czw', 'pt', 'sob', 'nd'];
const WEEKDAY_ON = ['', 'w poniedziałek', 'we wtorek', 'w środę', 'w czwartek', 'w piątek', 'w sobotę', 'w niedzielę'];

export function localDateString(timestamp: number, timeZone: string): string {
    const parts = new Intl.DateTimeFormat('en-CA', {
        timeZone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
    }).formatToParts(new Date(timestamp));

    const part = (type: string) => parts.find((item) => item.type === type)?.value ?? '';

    return `${part('year')}-${part('month')}-${part('day')}`;
}

export function describeRangeStatus(days: RangeDay[], now: number, timeZone: string): RangeStatus {
    const todayDate = localDateString(now, timeZone);
    const todayIndex = days.findIndex((day) => day.date === todayDate);
    const today = todayIndex >= 0 ? days[todayIndex] : null;
    const laterDays = todayIndex >= 0 ? days.slice(todayIndex + 1) : days.filter((day) => day.date > todayDate);
    const nextOpenDay = laterDays.find((day) => day.is_open) ?? null;

    const opensAt = today?.opens_at ? Date.parse(today.opens_at) : null;
    const closesAt = today?.closes_at ? Date.parse(today.closes_at) : null;

    if (today?.is_open && opensAt !== null && closesAt !== null) {
        if (now >= opensAt && now < closesAt) {
            return {
                state: 'open',
                today,
                nextOpenDay,
                minutesUntilClose: Math.ceil((closesAt - now) / 60000),
                minutesUntilOpen: 0,
            };
        }

        if (now < opensAt) {
            return {
                state: 'opens_later_today',
                today,
                nextOpenDay,
                minutesUntilClose: 0,
                minutesUntilOpen: Math.ceil((opensAt - now) / 60000),
            };
        }
    }

    return { state: 'closed_today', today, nextOpenDay, minutesUntilClose: 0, minutesUntilOpen: 0 };
}

export function isClosingSoon(status: RangeStatus): boolean {
    return status.state === 'open' && status.minutesUntilClose <= CLOSING_SOON_MINUTES;
}

export function formatTime(isoDate: string | null, timeZone: string): string {
    if (!isoDate) {
        return '';
    }

    return new Intl.DateTimeFormat('pl-PL', { timeZone, hour: 'numeric', minute: '2-digit' }).format(new Date(isoDate));
}

export function formatHours(day: RangeDay, timeZone: string): string {
    return `${formatTime(day.opens_at, timeZone)}–${formatTime(day.closes_at, timeZone)}`;
}

export function pluralPl(count: number, one: string, few: string, many: string): string {
    if (count === 1) {
        return one;
    }

    const lastDigit = count % 10;
    const lastTwoDigits = count % 100;

    if (lastDigit >= 2 && lastDigit <= 4 && (lastTwoDigits < 12 || lastTwoDigits > 14)) {
        return few;
    }

    return many;
}

export function formatDuration(totalMinutes: number): string {
    const hours = Math.floor(totalMinutes / 60);
    const minutes = totalMinutes % 60;
    const parts: string[] = [];

    if (hours > 0) {
        parts.push(`${hours} ${pluralPl(hours, 'godzina', 'godziny', 'godzin')}`);
    }

    if (minutes > 0 || hours === 0) {
        parts.push(`${minutes} ${pluralPl(minutes, 'minuta', 'minuty', 'minut')}`);
    }

    return parts.join(' ');
}

export function dayLabel(day: RangeDay, todayDate: string, tomorrowDate: string): string {
    if (day.date === todayDate) {
        return 'Dziś';
    }

    if (day.date === tomorrowDate) {
        return 'Jutro';
    }

    return WEEKDAY_SHORT[day.weekday] ?? '';
}

export function relativeDayPhrase(day: RangeDay, tomorrowDate: string): string {
    if (day.date === tomorrowDate) {
        return 'jutro';
    }

    return WEEKDAY_ON[day.weekday] ?? '';
}

export function shortDate(date: string): string {
    const [, month, day] = date.split('-');

    return `${Number(day)}.${month}`;
}

export function addDaysToDateString(date: string, days: number): string {
    const [year, month, day] = date.split('-').map(Number);
    const shifted = new Date(Date.UTC(year, month - 1, day + days));

    return shifted.toISOString().slice(0, 10);
}
