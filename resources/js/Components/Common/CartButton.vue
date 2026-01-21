<template>
    <div class="fixed top-4 right-4 z-50">
        <Link
            :href="route('cart.index')"
            class="relative bg-red-600 hover:bg-red-700 text-white p-3 rounded-full shadow-2xl border-2 border-red-500 transition-all duration-200 hover:scale-110 flex items-center justify-center"
        >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2 8h12l-2-8M7 13v8a2 2 0 002 2h6a2 2 0 002-2v-3"/>
            </svg>

            <!-- Counter badge -->
            <div
                v-if="itemCount > 0"
                class="absolute -top-2 -right-2 bg-yellow-500 text-black text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center border-2 border-red-600 min-w-[24px]"
            >
                {{ itemCount }}
            </div>
        </Link>
    </div>
</template>

<script setup>
import {Link} from '@inertiajs/vue3';
import {ref, onMounted} from 'vue';

const props = defineProps({
    cart: {
        type: Object,
        default: () => ({})
    }
});

const itemCount = ref(0);

const updateItemCount = () => {
    itemCount.value = Object.keys(props.cart).length;
};

onMounted(() => {
    updateItemCount();
});

// Watch for cart changes
import {watch} from 'vue';

watch(() => props.cart, updateItemCount, {deep: true});
</script>
