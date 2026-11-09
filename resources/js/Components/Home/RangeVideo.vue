<template>
    <div class="relative h-[460px] overflow-hidden rounded-3xl bg-stone-900 sm:h-[560px] lg:h-auto lg:min-h-[520px]">
        <video
            ref="video"
            class="absolute inset-0 h-full w-full object-cover"
            src="/videos/shotgun-lane.mp4"
            poster="/images/range/shotgun-lane-poster.jpg"
            muted
            loop
            playsinline
            preload="metadata"
            aria-label="Strzelanie ze strzelby na osi strzelnicy Sagittarius"
        />

        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 bg-gradient-to-t from-black/80 to-transparent p-5 pt-24 text-white sm:p-6">
            <div>
                <p class="text-sm font-semibold">Strzelnica Forty · Piątnica</p>
                <p class="mt-1 text-sm text-white/80">Pistolety, karabiny, strzelby i rewolwery na miejscu.</p>
            </div>
            <div class="flex shrink-0 gap-2">
                <button
                    v-if="!isPlaying"
                    type="button"
                    class="rounded-full border border-white/40 bg-black/40 px-3 py-1.5 text-xs font-semibold backdrop-blur transition-colors hover:bg-white hover:text-black"
                    @click="play"
                >
                    Odtwórz
                </button>
                <button
                    type="button"
                    class="rounded-full border border-white/40 bg-black/40 px-3 py-1.5 text-xs font-semibold backdrop-blur transition-colors hover:bg-white hover:text-black"
                    :aria-pressed="!isMuted"
                    @click="toggleSound"
                >
                    {{ isMuted ? 'Włącz dźwięk' : 'Wycisz' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';

const video = ref<HTMLVideoElement | null>(null);
const isMuted = ref(true);
const isPlaying = ref(false);

function play(): void {
    video.value
        ?.play()
        .then(() => {
            isPlaying.value = true;
        })
        .catch(() => {
            isPlaying.value = false;
        });
}

function toggleSound(): void {
    if (!video.value) {
        return;
    }

    isMuted.value = !isMuted.value;
    video.value.muted = isMuted.value;

    if (!isMuted.value) {
        video.value.currentTime = 0;
        play();
    }
}

onMounted(() => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!prefersReducedMotion) {
        play();
    }
});
</script>
