<?php

namespace Database\Seeders;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class GunsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $types = collect([
            'Pistolet',
            'Rewolwer',
            'Karabin',
            'Strzelba',
        ])->mapWithKeys(fn (string $name) => [
            $name => GunType::firstOrCreate(['name' => $name]),
        ]);

        $calibers = collect([
            '9x19',
            '.22 LR',
            '5.56x45 / .223 Rem',
            '7.62x39',
            '7.62x54R',
            '.357 Magnum',
            '.45 ACP',
            '12/70',
        ])->mapWithKeys(fn (string $name) => [
            $name => Caliber::firstOrCreate(['name' => $name]),
        ]);

        $ammo = [
            ['name' => '.22 LR', 'caliber' => '.22 LR', 'standard_price' => 1.50, 'club_price' => 1.00],
            ['name' => '9x19', 'caliber' => '9x19', 'standard_price' => 3.00, 'club_price' => 2.00],
            ['name' => '5.56x45 / .223 Rem', 'caliber' => '5.56x45 / .223 Rem', 'standard_price' => 6.00, 'club_price' => 4.00],
            ['name' => '7.62x39', 'caliber' => '7.62x39', 'standard_price' => 6.00, 'club_price' => 5.00],
            ['name' => '.357 Magnum', 'caliber' => '.357 Magnum', 'standard_price' => 6.00, 'club_price' => 4.00],
            ['name' => '.45 ACP', 'caliber' => '.45 ACP', 'standard_price' => 6.00, 'club_price' => 4.00],
            ['name' => '7.62x54R (Mosin)', 'caliber' => '7.62x54R', 'standard_price' => 8.00, 'club_price' => 5.00],
            ['name' => '12/70 Śrut', 'caliber' => '12/70', 'standard_price' => 8.00, 'club_price' => 5.00],
            ['name' => '12/70 Breneka', 'caliber' => '12/70', 'standard_price' => 9.00, 'club_price' => 6.00],
        ];

        foreach ($ammo as $row) {
            $caliber = $calibers[$row['caliber']];

            Ammunition::updateOrCreate(
                ['name' => $row['name'], 'caliber_id' => $caliber->id],
                ['standard_price' => $row['standard_price'], 'club_price' => $row['club_price']],
            );
        }

        $guns = [
            ['name' => 'CZ 75 Shadow 1', 'type' => 'Pistolet', 'caliber' => '9x19'],
            ['name' => 'CZ P-09', 'type' => 'Pistolet', 'caliber' => '9x19'],
            ['name' => 'CZ P-10 F', 'type' => 'Pistolet', 'caliber' => '9x19'],
            ['name' => 'CZ 75 Compact', 'type' => 'Pistolet', 'caliber' => '9x19'],

            ['name' => 'Ruger GP100 (3")', 'type' => 'Rewolwer', 'caliber' => '.357 Magnum'],

            ['name' => 'Mossberg 500', 'type' => 'Strzelba', 'caliber' => '12/70'],
            ['name' => 'Baikal IZH-27 / MP-27', 'type' => 'Strzelba', 'caliber' => '12/70'],

            ['name' => 'WBP Jack 556', 'type' => 'Karabin', 'caliber' => '5.56x45 / .223 Rem'],
            ['name' => 'BURGU BRG55 (AR-15)', 'type' => 'Karabin', 'caliber' => '5.56x45 / .223 Rem'],
            ['name' => 'Diamondback DB15 (AR-15)', 'type' => 'Karabin', 'caliber' => '5.56x45 / .223 Rem'],

            ['name' => 'WBP Mini Jack (7.62x39)', 'type' => 'Karabin', 'caliber' => '7.62x39'],
            ['name' => 'Mosin-Nagant (np. 91/30)', 'type' => 'Karabin', 'caliber' => '7.62x54R'],

            ['name' => 'Ruger Mark IV 22/45', 'type' => 'Pistolet', 'caliber' => '.22 LR'],
            ['name' => 'ALFA Steel (revolver)', 'type' => 'Rewolwer', 'caliber' => '.357 Magnum'],
            ['name' => 'CZ 457', 'type' => 'Karabin', 'caliber' => '.22 LR'],
            ['name' => 'CZ 455', 'type' => 'Karabin', 'caliber' => '.22 LR'],
            ['name' => 'Hammerli TAC R1 (.22 LR)', 'type' => 'Karabin', 'caliber' => '.22 LR'],

            ['name' => 'Celik Crossline (9x19)', 'type' => 'Karabin', 'caliber' => '9x19'],
            ['name' => 'Springfield Echelon', 'type' => 'Pistolet', 'caliber' => '9x19'],
            ['name' => 'Ruger PC Carbine', 'type' => 'Karabin', 'caliber' => '9x19'],
            ['name' => 'Glock 19', 'type' => 'Pistolet', 'caliber' => '9x19'],
            ['name' => 'Canik SFx Rival-S', 'type' => 'Pistolet', 'caliber' => '9x19'],

            ['name' => 'HK SP5 (MP5)', 'type' => 'Karabin', 'caliber' => '9x19'],
            ['name' => 'CZ Scorpion EVO 3', 'type' => 'Karabin', 'caliber' => '9x19'],

            ['name' => 'Rossi Puma (.357)', 'type' => 'Karabin', 'caliber' => '.357 Magnum'],

            ['name' => 'FX-9 (9mm)', 'type' => 'Karabin', 'caliber' => '9x19'],
        ];

        foreach ($guns as $gun) {
            $type = $types[$gun['type']];
            $caliber = $calibers[$gun['caliber']];

            Gun::updateOrCreate(
                ['name' => $gun['name']],
                [
                    'gun_type_id' => $type->id,
                    'caliber_id' => $caliber->id,
                    'description' => null,
                    'photos' => [],
                ],
            );
        }
    }
}
