<template>
    <div class="min-h-screen bg-white text-black">
        <SimpleNavbar :cart="cart" />
        <GunsHeader />
        <GunsGrid
            :guns="guns"
            :gun-types="gunTypes"
            :gun-packages="gunPackages"
            :cart="cart"
            @add-to-cart="handleAddToCart"
            @add-package-to-cart="handleAddPackageToCart"
        />
        <Footer />
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import Footer from '../../Components/Common/Footer.vue';
import SimpleNavbar from '../../Components/Common/SimpleNavbar.vue';
import GunsGrid from '../../Components/Guns/GunsGrid.vue';
import GunsHeader from '../../Components/Guns/GunsHeader.vue';
import type { CartMap, Gun, GunPackage, GunType } from '@/types/storefront';

withDefaults(
    defineProps<{
        guns?: Gun[];
        gunTypes?: GunType[];
        gunPackages?: GunPackage[];
        cart?: CartMap;
    }>(),
    {
        guns: () => [],
        gunTypes: () => [],
        gunPackages: () => [],
        cart: () => ({}),
    }
);

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
