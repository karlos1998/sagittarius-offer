<template>
    <div v-if="isEnabled" class="space-y-2">
        <div ref="container" class="min-h-[65px]"></div>
        <p v-if="scriptFailed" class="text-sm text-red-600">
            Nie udało się załadować zabezpieczenia. Odśwież stronę i spróbuj ponownie.
        </p>
    </div>
</template>

<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

type TurnstileApi = {
    render: (container: HTMLElement, options: Record<string, unknown>) => string;
    reset: (widgetId?: string) => void;
    remove: (widgetId: string) => void;
};

declare global {
    interface Window {
        turnstile?: TurnstileApi;
    }
}

const props = withDefaults(
    defineProps<{
        enabled?: boolean;
        siteKey?: string | null;
        action?: string;
        resetKey?: number;
    }>(),
    {
        enabled: false,
        siteKey: null,
        action: 'checkout',
        resetKey: 0,
    }
);

const emit = defineEmits<{
    verified: [token: string];
    expired: [];
    error: [];
}>();

const scriptId = 'cloudflare-turnstile-script';
const scriptUrl = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
const container = ref<HTMLElement | null>(null);
const scriptFailed = ref(false);
let widgetId: string | null = null;

const isEnabled = computed(() => props.enabled && Boolean(props.siteKey));

onMounted(() => {
    void renderWidget();
});

onBeforeUnmount(() => {
    removeWidget();
});

watch(
    () => props.resetKey,
    () => {
        resetWidget();
    }
);

async function renderWidget(): Promise<void> {
    if (!isEnabled.value || !props.siteKey || !container.value || widgetId !== null) {
        return;
    }

    scriptFailed.value = false;

    try {
        await loadTurnstileScript();
    } catch {
        scriptFailed.value = true;
        emit('error');

        return;
    }

    if (!window.turnstile || !container.value) {
        scriptFailed.value = true;
        emit('error');

        return;
    }

    widgetId = window.turnstile.render(container.value, {
        sitekey: props.siteKey,
        action: props.action,
        callback: (token: string): void => emit('verified', token),
        'expired-callback': (): void => emit('expired'),
        'error-callback': (): void => emit('error'),
    });
}

function resetWidget(): void {
    if (!widgetId || !window.turnstile) {
        void renderWidget();

        return;
    }

    window.turnstile.reset(widgetId);
}

function removeWidget(): void {
    if (!widgetId || !window.turnstile) {
        return;
    }

    window.turnstile.remove(widgetId);
    widgetId = null;
}

function loadTurnstileScript(): Promise<void> {
    if (window.turnstile) {
        return Promise.resolve();
    }

    const existingScript = document.getElementById(scriptId) as HTMLScriptElement | null;

    if (existingScript) {
        return waitForScript(existingScript);
    }

    const script = document.createElement('script');
    script.id = scriptId;
    script.src = scriptUrl;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);

    return waitForScript(script);
}

function waitForScript(script: HTMLScriptElement): Promise<void> {
    return new Promise((resolve, reject) => {
        script.addEventListener('load', () => resolve(), { once: true });
        script.addEventListener('error', () => reject(), { once: true });
    });
}
</script>
