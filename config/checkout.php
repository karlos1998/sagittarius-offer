<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Voucher Email Verification
    |--------------------------------------------------------------------------
    |
    | When enabled, customers must confirm a PIN code sent by email before the
    | voucher PDF is issued. When disabled, the voucher is created immediately
    | after submitting checkout details.
    |
    */

    'require_voucher_email_verification' => env('CHECKOUT_REQUIRE_VOUCHER_EMAIL_VERIFICATION', false),

];
