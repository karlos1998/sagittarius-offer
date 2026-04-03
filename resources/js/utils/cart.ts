import type { CartDisplayItem, CartMap, Gun } from '@/types/storefront';
import { toNumber } from '@/utils/format';

export function countCartItems(cart: CartMap): number {
    return Object.keys(cart ?? {}).length;
}

export function buildCartDisplayItems(
    cart: CartMap,
    guns: Gun[],
    isClubMember = false
): CartDisplayItem[] {
    return Object.entries(cart ?? {})
        .map(([gunId, cartItem]) => {
            const gun = guns.find((item) => item.id === Number(gunId));

            if (!gun) {
                return null;
            }

            const ammunitionItems = Object.entries(cartItem.ammunitions ?? {})
                .map(([ammoId, quantity]) => {
                    const ammunition = gun.caliber?.ammunitions?.find((item) => item.id === Number(ammoId));

                    if (!ammunition) {
                        return null;
                    }

                    const shots = toNumber(quantity);
                    const pricePerShot = isClubMember
                        ? toNumber(ammunition.club_price)
                        : toNumber(ammunition.standard_price);

                    return {
                        ammunition,
                        quantity: shots,
                        total: pricePerShot * shots,
                    };
                })
                .filter((item): item is NonNullable<typeof item> => item !== null);

            return {
                gun,
                cartItem,
                ammunitionItems,
            };
        })
        .filter((item): item is CartDisplayItem => item !== null);
}

export function calculateTotalShots(items: CartDisplayItem[]): number {
    return items.reduce((total, item) => {
        return total + item.ammunitionItems.reduce((itemTotal, ammoItem) => itemTotal + ammoItem.quantity, 0);
    }, 0);
}

export function calculateTotalPrice(items: CartDisplayItem[]): number {
    return items.reduce((total, item) => {
        return total + item.ammunitionItems.reduce((itemTotal, ammoItem) => itemTotal + ammoItem.total, 0);
    }, 0);
}

export function calculateCartValueFromRawData(cart: CartMap, guns: Gun[], isClubMember = false): number {
    const items = buildCartDisplayItems(cart, guns, isClubMember);

    return calculateTotalPrice(items);
}
