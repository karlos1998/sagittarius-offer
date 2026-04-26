<script setup lang="ts">
import Modal from './Modal.vue';

const emit = defineEmits(['close']);

withDefaults(
    defineProps<{
        show?: boolean;
        maxWidth?: string;
        closeable?: boolean;
    }>(),
    {
        show: false,
        maxWidth: '2xl',
        closeable: true,
    }
);

const close = (): void => {
    emit('close');
};
</script>

<template>
    <Modal
        :show="show"
        :max-width="maxWidth"
        :closeable="closeable"
        @close="close"
    >
        <div class="bg-white px-5 py-5 sm:px-7 sm:py-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
                <div
                    class="mx-auto flex size-12 shrink-0 items-center justify-center rounded-full border border-amber-200 bg-amber-50 sm:mx-0"
                >
                    <svg class="size-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>

                <div class="text-center sm:text-left">
                    <h3 class="text-lg font-semibold leading-6 text-gray-900 sm:text-xl">
                        <slot name="title" />
                    </h3>

                    <div class="mt-3 text-sm leading-6 text-gray-600 sm:text-[15px]">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 bg-gray-50 px-5 py-4 sm:px-7">
            <slot name="footer" />
        </div>
    </Modal>
</template>
