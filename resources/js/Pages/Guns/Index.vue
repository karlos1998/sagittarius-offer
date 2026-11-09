<template>
    <div class="min-h-screen bg-white text-black">
        <Head title="Broń i ceny" />
        <SimpleNavbar :cart="cart" />
        <GunsHeader />
        <GunsGrid
            :guns="guns"
            :gun-types="gunTypes"
            :gun-packages="gunPackages"
            :cart="cart"
            :is-club-member="clubMember"
            @add-to-cart="handleAddToCart"
            @update:is-club-member="handleClubMemberChange"
            @add-package-to-cart="handleAddPackageToCart"
        />
        <Footer />
    </div>
</template>

<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import GunsGrid from '../../Components/Guns/GunsGrid.vue';
import GunsHeader from '../../Components/Guns/GunsHeader.vue';
import type { CartMap, Gun, GunPackage, GunType } from '@/types/storefront';

const props = withDefaults(
    defineProps<{
        guns?: Gun[];
        gunTypes?: GunType[];
        gunPackages?: GunPackage[];
        cart?: CartMap;
        isClubMember?: boolean;
    }>(),
    {
        guns: () => [],
        gunTypes: () => [],
        gunPackages: () => [],
        cart: () => ({}),
        isClubMember: false,
    }
);

const clubMember = ref(props.isClubMember);

watch(
    () => props.isClubMember,
    (value) => {
        clubMember.value = value;
    }
);

function handleClubMemberChange(value: boolean): void {
    clubMember.value = value;
    router.post(
        route('cart.toggle-club-member'),
        {
            is_club_member: value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        }
    );
}

function handleAddToCart(gunId: number): void {
    router.post(
        route('cart.add'),
        {
            gun_id: gunId,
        },
        {
            preserveScroll: true,
        }
    );
}

function handleAddPackageToCart(packageId: number): void {
    router.post(
        route('cart.add-package'),
        {
            package_id: packageId,
        },
        {
            preserveScroll: true,
        }
    );
}
</script>
