export interface Ammunition {
    id: number;
    name: string;
    standard_price: number | string;
    club_price: number | string;
}

export interface Caliber {
    id: number;
    name: string;
    ammunitions?: Ammunition[];
}

export interface GunType {
    id: number;
    name: string;
}

export interface Gun {
    id: number;
    name: string;
    description?: string | null;
    photos?: string[];
    photo_urls?: string[];
    gun_type_id?: number | null;
    gun_type?: GunType | null;
    gunType?: GunType | null;
    caliber?: Caliber | null;
}

export interface GunPackageGun {
    id: number;
    gun_id: number;
    ammunition_id: number;
    shots_quantity: number;
    sort_order: number;
    gun?: Gun | null;
    ammunition?: Ammunition | null;
}

export interface GunPackage {
    id: number;
    name: string;
    description?: string | null;
    preview_image?: string | null;
    preview_image_url?: string | null;
    package_guns?: GunPackageGun[];
}

export interface Instructor {
    id: number;
    full_name: string;
    description?: string | null;
    photo_url?: string | null;
}

export interface CartSessionItem {
    gun_id: number;
    ammunitions: Record<string, number>;
    package_id?: number | null;
    package_name?: string | null;
    package_guns?: string[];
}

export type CartMap = Record<string, CartSessionItem>;

export interface CartAmmunitionItem {
    ammunition: Ammunition;
    quantity: number;
    total: number;
}

export interface CartDisplayItem {
    gun: Gun;
    cartItem: CartSessionItem;
    ammunitionItems: CartAmmunitionItem[];
}

export interface PaymentMethodOption {
    value: string;
    label: string;
}

export interface CheckoutOrderItem {
    gun_name: string;
    ammunition_name: string;
    gun_package_name?: string | null;
    gun_package_guns_summary?: string | null;
    quantity: number;
    line_total: number | string;
}

export interface CheckoutOrder {
    id: number;
    order_number: string;
    email: string;
    full_address: string;
    payment_method_label: string;
    payment_status_label: string;
    total_shots: number | string;
    total_amount: number | string;
    verification_code_expires_at?: string | null;
    verification_code_valid_for_minutes?: number;
    download_url?: string | null;
    items?: CheckoutOrderItem[];
}

export interface PanelOrder {
    id: number;
    order_number: string;
    customer_full_name: string;
    email: string;
    status_label: string;
    payment_status: string;
    payment_status_label: string;
    total_amount: number | string;
    is_completed: boolean;
    completed_at?: string | null;
    created_at?: string | null;
}
