<template>
    <div class="sticky top-14 z-30 -mx-4 mb-8 border-b border-black/10 bg-white/95 px-4 py-3 backdrop-blur sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="-mx-1 flex gap-2 overflow-x-auto px-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                <button
                    v-for="option in typeOptions"
                    :key="option.id ?? 'all'"
                    type="button"
                    :class="[
                        'shrink-0 rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                        selectedType === option.id
                            ? 'border-black bg-black text-white'
                            : 'border-black/15 bg-white text-black hover:border-black',
                    ]"
                    @click="selectedType = option.id"
                >
                    {{ option.name }}
                    <span :class="selectedType === option.id ? 'text-white/60' : 'text-black/40'">{{ option.count }}</span>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-sm text-black/60">Ceny dla:</span>
                <div class="inline-flex rounded-full border border-black/15 bg-stone-100 p-1 text-sm font-medium">
                    <button
                        type="button"
                        :aria-pressed="!isClubMember"
                        :class="['rounded-full px-3 py-1 transition-colors', !isClubMember ? 'bg-white text-black shadow-sm' : 'text-black/60 hover:text-black']"
                        @click="setClubMember(false)"
                    >
                        Gościa
                    </button>
                    <button
                        type="button"
                        :aria-pressed="isClubMember"
                        :class="['rounded-full px-3 py-1 transition-colors', isClubMember ? 'bg-amber-300 text-black shadow-sm' : 'text-black/60 hover:text-black']"
                        @click="setClubMember(true)"
                    >
                        Klubowicza
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Gun, GunType } from '@/types/storefront';

const props = withDefaults(
    defineProps<{
        gunTypes?: GunType[];
        guns?: Gun[];
        modelValue?: number | null;
        isClubMember?: boolean;
    }>(),
    {
        gunTypes: () => [],
        guns: () => [],
        modelValue: null,
        isClubMember: false,
    }
);

const emit = defineEmits<{
    (event: 'update:modelValue', value: number | null): void;
    (event: 'update:isClubMember', value: boolean): void;
}>();

const selectedType = computed({
    get: () => props.modelValue,
    set: (value: number | null) => emit('update:modelValue', value),
});

const typeOptions = computed(() => [
    { id: null, name: 'Wszystkie', count: props.guns.length },
    ...props.gunTypes
        .map((type) => ({
            id: type.id as number | null,
            name: type.name,
            count: props.guns.filter((gun) => gun.gun_type_id === type.id).length,
        }))
        .filter((option) => option.count > 0),
]);

function setClubMember(value: boolean): void {
    if (value !== props.isClubMember) {
        emit('update:isClubMember', value);
    }
}
</script>
