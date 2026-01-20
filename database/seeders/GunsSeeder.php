<?php

namespace Database\Seeders;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GunsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create gun types if they don't exist
        $pistolType = GunType::firstOrCreate(['name' => 'Pistolet']);
        $rifleType = GunType::firstOrCreate(['name' => 'Karabin']);
        $shotgunType = GunType::firstOrCreate(['name' => 'Strzelba']);

        // Create calibers if they don't exist
        $caliber9mm = Caliber::firstOrCreate(['name' => '9mm']);
        $caliber22lr = Caliber::firstOrCreate(['name' => '.22 LR']);
        $caliber12gauge = Caliber::firstOrCreate(['name' => '12 Gauge']);
        $caliber308 = Caliber::firstOrCreate(['name' => '.308 Winchester']);

        // Create ammunitions for each caliber
        Ammunition::firstOrCreate([
            'name' => '9mm Luger FMJ',
            'caliber_id' => $caliber9mm->id,
            'club_price' => 0.35,
            'standard_price' => 0.45,
        ]);

        Ammunition::firstOrCreate([
            'name' => '9mm Luger JHP',
            'caliber_id' => $caliber9mm->id,
            'club_price' => 0.40,
            'standard_price' => 0.55,
        ]);

        Ammunition::firstOrCreate([
            'name' => '.22 LR Standard',
            'caliber_id' => $caliber22lr->id,
            'club_price' => 0.08,
            'standard_price' => 0.12,
        ]);

        Ammunition::firstOrCreate([
            'name' => '.22 LR Subsonic',
            'caliber_id' => $caliber22lr->id,
            'club_price' => 0.10,
            'standard_price' => 0.15,
        ]);

        Ammunition::firstOrCreate([
            'name' => '12 Gauge Slug',
            'caliber_id' => $caliber12gauge->id,
            'club_price' => 0.80,
            'standard_price' => 1.20,
        ]);

        Ammunition::firstOrCreate([
            'name' => '12 Gauge #7.5',
            'caliber_id' => $caliber12gauge->id,
            'club_price' => 0.25,
            'standard_price' => 0.35,
        ]);

        Ammunition::firstOrCreate([
            'name' => '.308 Winchester FMJ',
            'caliber_id' => $caliber308->id,
            'club_price' => 0.65,
            'standard_price' => 0.85,
        ]);

        Ammunition::firstOrCreate([
            'name' => '.308 Winchester Soft Point',
            'caliber_id' => $caliber308->id,
            'club_price' => 0.75,
            'standard_price' => 1.05,
        ]);

        // Create sample guns
        Gun::create([
            'name' => 'Glock 17',
            'gun_type_id' => $pistolType->id,
            'caliber_id' => $caliber9mm->id,
            'description' => 'Profesjonalny pistolet samopowtarzalny, idealny do treningów strzeleckich.',
            'photos' => []
        ]);

        Gun::create([
            'name' => 'CZ 75',
            'gun_type_id' => $pistolType->id,
            'caliber_id' => $caliber9mm->id,
            'description' => 'Legendarny pistolet czeski, znany ze swojej niezawodności i precyzji.',
            'photos' => []
        ]);

        Gun::create([
            'name' => 'Ruger 10/22',
            'gun_type_id' => $rifleType->id,
            'caliber_id' => $caliber22lr->id,
            'description' => 'Popularny karabin do treningów, lekki i łatwy w obsłudze.',
            'photos' => []
        ]);

        Gun::create([
            'name' => 'Remington 870',
            'gun_type_id' => $shotgunType->id,
            'caliber_id' => $caliber12gauge->id,
            'description' => 'Klasyczna strzelba gładkolufowa, niezawodna i wszechstronna.',
            'photos' => []
        ]);

        Gun::create([
            'name' => 'Tikka T3x',
            'gun_type_id' => $rifleType->id,
            'caliber_id' => $caliber308->id,
            'description' => 'Precyzyjny karabin myśliwski z regulowanym mechanizmem spustowym.',
            'photos' => []
        ]);
    }
}
