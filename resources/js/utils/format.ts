export function toNumber(value: number | string | null | undefined): number {
    const numericValue = Number(value ?? 0);

    if (Number.isNaN(numericValue)) {
        return 0;
    }

    return numericValue;
}

export function formatPrice(value: number | string | null | undefined): string {
    return toNumber(value).toFixed(2);
}

export function formatCurrencyPLN(value: number | string | null | undefined): string {
    return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN',
        minimumFractionDigits: 2,
    }).format(toNumber(value));
}
