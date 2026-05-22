<template>
    <div class="relative aspect-video overflow-hidden border-b border-black/20 bg-white">
        <button
            v-if="hasImage"
            type="button"
            class="group/photo relative flex h-full w-full items-center justify-center p-2 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-black"
            :aria-label="`Powiększ zdjęcie: ${alt}`"
            @blur="closePreview"
            @click="handleClick"
            @focus="schedulePreview"
            @pointerenter="handlePointerEnter"
            @pointerleave="handlePointerLeave"
            @pointerup="handlePointerUp"
        >
            <img
                :src="src"
                :alt="alt"
                class="h-auto max-h-full w-auto max-w-full object-contain transition duration-300 ease-out group-hover/photo:scale-[1.03] group-focus-visible/photo:scale-[1.03]"
                draggable="false"
                @error="handleImageError"
            >

            <span
                class="pointer-events-none absolute right-3 top-3 inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/80 bg-white/90 text-black/75 opacity-100 shadow-lg backdrop-blur transition duration-200 group-hover/photo:scale-105 group-hover/photo:text-black sm:opacity-0 sm:group-hover/photo:opacity-100 sm:group-focus-visible/photo:opacity-100"
                title="Powiększ zdjęcie"
                aria-hidden="true"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.1-5.15a6.25 6.25 0 11-12.5 0 6.25 6.25 0 0112.5 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 8.5v6m-3-3h6" />
                </svg>
            </span>
        </button>

        <div v-else class="flex h-full w-full items-center justify-center">
            <svg class="h-12 w-12 text-black/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-[0.98]"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-[0.98]"
        >
            <div
                v-if="isPreviewOpen && hasImage"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-sm sm:p-8"
                :class="isHoverCapable ? 'pointer-events-none' : 'pointer-events-auto'"
                @click.self="closePreview"
            >
                <div class="relative flex max-h-[90vh] w-full max-w-6xl items-center justify-center">
                    <button
                        v-if="!isHoverCapable"
                        type="button"
                        class="absolute right-0 top-0 z-10 inline-flex h-10 w-10 -translate-y-12 items-center justify-center rounded-full border border-white/60 bg-white/95 text-black shadow-xl transition hover:bg-black hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:-right-3 sm:-top-3 sm:translate-y-0"
                        aria-label="Zamknij podgląd"
                        @click="closePreview"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <img
                        :src="src"
                        :alt="alt"
                        class="max-h-[84vh] max-w-full rounded bg-white/5 object-contain shadow-2xl ring-1 ring-white/20"
                        draggable="false"
                        @error="handleImageError"
                    >
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

const props = withDefaults(
    defineProps<{
        src?: string | null;
        alt: string;
    }>(),
    {
        src: null,
    }
);

const imageFailed = ref(false);
const isHoverCapable = ref(false);
const isPreviewOpen = ref(false);

let previewTimer: ReturnType<typeof setTimeout> | null = null;
let removeHoverMediaListener: (() => void) | null = null;

const hasImage = computed(() => Boolean(props.src) && !imageFailed.value);

watch(
    () => props.src,
    () => {
        imageFailed.value = false;
        closePreview();
    }
);

onMounted(() => {
    const hoverMedia = window.matchMedia('(hover: hover) and (pointer: fine)');
    const syncHoverCapability = (): void => {
        isHoverCapable.value = hoverMedia.matches;
    };

    syncHoverCapability();

    hoverMedia.addEventListener('change', syncHoverCapability);
    removeHoverMediaListener = (): void => hoverMedia.removeEventListener('change', syncHoverCapability);

    window.addEventListener('keydown', handleWindowKeydown);
});

onBeforeUnmount(() => {
    clearPreviewTimer();
    removeHoverMediaListener?.();
    window.removeEventListener('keydown', handleWindowKeydown);
});

function handlePointerEnter(event: PointerEvent): void {
    if (event.pointerType === 'mouse') {
        schedulePreview();
    }
}

function handlePointerLeave(event: PointerEvent): void {
    if (event.pointerType === 'mouse') {
        closePreview();
    }
}

function handlePointerUp(event: PointerEvent): void {
    if (event.pointerType !== 'mouse') {
        openPreview();
    }
}

function handleClick(event: MouseEvent): void {
    if (event.detail === 0) {
        openPreview();
    }
}

function handleWindowKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        closePreview();
    }
}

function schedulePreview(): void {
    if (!hasImage.value) {
        return;
    }

    clearPreviewTimer();
    previewTimer = setTimeout(openPreview, 200);
}

function openPreview(): void {
    if (!hasImage.value) {
        return;
    }

    isPreviewOpen.value = true;
}

function closePreview(): void {
    clearPreviewTimer();
    isPreviewOpen.value = false;
}

function clearPreviewTimer(): void {
    if (previewTimer === null) {
        return;
    }

    clearTimeout(previewTimer);
    previewTimer = null;
}

function handleImageError(): void {
    imageFailed.value = true;
    closePreview();
}
</script>
