<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('orders', 'street')) {
                $table->string('street')->default('')->after('last_name');
            }

            if (! Schema::hasColumn('orders', 'house_number')) {
                $table->string('house_number', 20)->default('')->after('street');
            }

            if (! Schema::hasColumn('orders', 'apartment_number')) {
                $table->string('apartment_number', 20)->nullable()->after('house_number');
            }

            if (! Schema::hasColumn('orders', 'postal_code')) {
                $table->string('postal_code', 10)->default('')->after('apartment_number');
            }

            if (! Schema::hasColumn('orders', 'city')) {
                $table->string('city')->default('')->after('postal_code');
            }
        });

        if (Schema::hasColumn('orders', 'address')) {
            DB::table('orders')
                ->where('street', '')
                ->update(['street' => DB::raw('address')]);

            Schema::table('orders', function (Blueprint $table): void {
                $table->dropColumn('address');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('orders')) {
            return;
        }

        if (! Schema::hasColumn('orders', 'address')) {
            Schema::table('orders', function (Blueprint $table): void {
                $table->string('address')->default('')->after('last_name');
            });
        }

        DB::table('orders')->update([
            'address' => DB::raw("TRIM(CONCAT(street, ' ', house_number, IF(apartment_number IS NULL OR apartment_number = '', '', CONCAT('/', apartment_number)), ', ', postal_code, ' ', city))"),
        ]);

        Schema::table('orders', function (Blueprint $table): void {
            $columnsToDrop = [];

            foreach (['street', 'house_number', 'apartment_number', 'postal_code', 'city'] as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
