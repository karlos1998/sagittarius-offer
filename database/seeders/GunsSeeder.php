<?php

namespace Database\Seeders;

use App\Models\Ammunition;
use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunPackage;
use App\Models\GunType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            [
                'name' => 'CZ 75 Shadow 1 (SP-01 Shadow)',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Pełnowymiarowy pistolet SA/DA oparty na platformie CZ 75 SP-01, popularny w strzelectwie dynamicznym. Łączy klasyczną konstrukcję CZ z udoskonaloną ergonomią.',
            ],
            [
                'name' => 'CZ P-09',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Pełnowymiarowy pistolet SA/DA z polimerowym szkieletem wzmacnianym włóknem szklanym. Ma szynę akcesoryjną i wymienne nakładki chwytu.',
            ],
            [
                'name' => 'CZ P-10 F',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Pełnowymiarowy pistolet bijnikowy (striker-fired) z rodziny P-10. Wersja F oferuje powiększoną pojemność magazynka i dłuższą linię celowania.',
            ],
            [
                'name' => 'CZ 75 Compact',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Kompaktowa wersja klasycznej CZ 75 ze stalowym szkieletem. Zachowuje charakterystykę strzelania serii CZ 75 w mniejszych gabarytach.',
            ],

            [
                'name' => 'Ruger GP100 (3")',
                'type' => 'Rewolwer',
                'caliber' => '.357 Magnum',
                'description' => 'Rewolwer double-action Ruger GP100 w kalibrze .357 Magnum. Seria GP100 jest oferowana w różnych konfiguracjach lufy i pojemności bębna.',
            ],

            [
                'name' => 'Mossberg 500',
                'type' => 'Strzelba',
                'caliber' => '12/70',
                'description' => 'Seria strzelb powtarzalnych (pump-action) produkowana przez O.F. Mossberg & Sons od 1961 roku. Występuje w wielu wariantach konfiguracji i przeznaczenia.',
            ],
            [
                'name' => 'Baikal IZH-27 / MP-27',
                'type' => 'Strzelba',
                'caliber' => '12/70',
                'description' => 'Dwulufowa strzelba łamana typu over-and-under (nadlufka) z zakładów w Iżewsku. Model produkowany jest seryjnie od lat 70. i występuje w kilku kalibrach.',
            ],

            [
                'name' => 'WBP Jack 556',
                'type' => 'Karabin',
                'caliber' => '5.56x45 / .223 Rem',
                'description' => 'Samopowtarzalny karabinek w stylu AK, produkowany w Polsce przez WBP i oferowany m.in. w 5.56×45 /.223 Rem. Seria Jack to odmiany karabinków w układzie AKM.',
            ],
            [
                'name' => 'BURGU BRG55 (AR-15)',
                'type' => 'Karabin',
                'caliber' => '5.56x45 / .223 Rem',
                'description' => 'Karabinek oparty o platformę AR-15 w kalibrze 5.56×45 /.223 Rem. W materiałach produktowych często podkreślany jest układ gazowy z tłokiem o krótkim skoku.',
            ],
            [
                'name' => 'Diamondback DB15 (AR-15)',
                'type' => 'Karabin',
                'caliber' => '5.56x45 / .223 Rem',
                'description' => 'Rodzina karabinków DB15 na platformie AR-15, oferowana m.in. w 5.56 NATO. Występuje w wielu wariantach (różne lufy, łoża i konfiguracje).',
            ],

            [
                'name' => 'WBP Mini Jack (7.62x39)',
                'type' => 'Karabin',
                'caliber' => '7.62x39',
                'description' => 'Samopowtarzalny subkarabinek w kalibrze 7.62×39 produkowany przez WBP. Jest oparty o konstrukcję AKM i wyróżnia się skróconą konfiguracją.',
            ],
            [
                'name' => 'Mosin-Nagant (np. 91/30)',
                'type' => 'Karabin',
                'caliber' => '7.62x54R',
                'description' => 'Historyczny karabin powtarzalny (bolt-action) z integralnym magazynkiem na 5 nabojów. Standardowo kojarzony z nabojem 7.62×54R i masową produkcją wojskową.',
            ],

            [
                'name' => 'Ruger Mark IV 22/45',
                'type' => 'Pistolet',
                'caliber' => '.22 LR',
                'description' => 'Pistolet bocznego zapłonu w kalibrze .22 LR z serii Ruger Mark IV. Wariant 22/45 nawiązuje ergonomią do pistoletów centralnego zapłonu.',
            ],
            [
                'name' => 'ALFA Steel (revolver)',
                'type' => 'Rewolwer',
                'caliber' => '.357 Magnum',
                'description' => 'Stalowy rewolwer Alfa-Proj w kalibrze .357 Magnum (często także z obsługą .38 Special). Spotykany w wersjach z regulowanymi przyrządami celowniczymi.',
            ],
            [
                'name' => 'CZ 457',
                'type' => 'Karabin',
                'caliber' => '.22 LR',
                'description' => 'Seria karabinów bocznego zapłonu CZ 457 o modułowej konstrukcji. Producent wskazuje na łatwą personalizację i możliwość konwersji w obrębie rodziny (.22 LR / .17 HMR / .22 WMR).',
            ],
            [
                'name' => 'CZ 455',
                'type' => 'Karabin',
                'caliber' => '.22 LR',
                'description' => 'Magazynkowy karabin powtarzalny (bolt-action) bocznego zapłonu z rodziny CZ 455. Seria była produkowana w latach 2010–2018 i występowała w kilku kalibrach rimfire.',
            ],
            [
                'name' => 'Hämmerli TAC R1 (.22 LR)',
                'type' => 'Karabin',
                'caliber' => '.22 LR',
                'description' => 'Samopowtarzalny karabinek treningowo-sportowy w kalibrze .22 LR o “taktycznej” ergonomii. Producent akcentuje m.in. łoże M-LOK i składane przyrządy celownicze.',
            ],

            [
                'name' => 'Celik Crossline (9x19)',
                'type' => 'Karabin',
                'caliber' => '9x19',
                'description' => 'Karabinek w kalibrze 9×19, oferowany m.in. w długościach luf 8" i 16". Opisy produktu wskazują na rozwiązania sterowania inspirowane AR-15 i elementy obustronne.',
            ],
            [
                'name' => 'Springfield Echelon',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Modułowy pistolet bijnikowy 9×19 z wymiennym stalowym “chassis” (COG) i systemem montażu optyki. Linia została wprowadzona w 2023 roku.',
            ],
            [
                'name' => 'Ruger PC Carbine',
                'type' => 'Karabin',
                'caliber' => '9x19',
                'description' => 'Samopowtarzalny karabinek na amunicję pistoletową 9×19, z prostym odrzutem zamka (blowback). Nowa odsłona PC Carbine wróciła do produkcji jako wersja “takedown” od 2017 roku.',
            ],
            [
                'name' => 'Glock 19',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Kompaktowy pistolet 9 mm Luger, projektowany jako uniwersalny “all-round”. Producent wskazuje na zredukowane wymiary względem pełnowymiarowych modeli.',
            ],
            [
                'name' => 'Canik SFx Rival-S',
                'type' => 'Pistolet',
                'caliber' => '9x19',
                'description' => 'Pistolet bijnikowy z metalowym (stalowym) szkieletem, kierowany pod strzelectwo sportowe. Producent podkreśla stalową ramę i rozwiązania pod dynamikę.',
            ],

            [
                'name' => 'HK SP5',
                'type' => 'Karabin',
                'caliber' => '9x19',
                'description' => 'Półautomatyczna, cywilna wersja legendarnego MP5 w kalibrze 9×19. Wyróżnia ją mechanizm opóźnionego odrzutu rolkowego (roller-delayed blowback).',
            ],
            [
                'name' => 'CZ Scorpion EVO 3 S1',
                'type' => 'Karabin',
                'caliber' => '9x19',
                'description' => 'Platforma 9×19 produkowana przez CZ, dostępna m.in. jako wersja cywilna S1 (carbine/pistol). Opisy producenta wskazują na ambi sterowanie i szerokie możliwości doposażenia.',
            ],

            [
                'name' => 'Rossi Puma (.357)',
                'type' => 'Karabin',
                'caliber' => '.357 Magnum',
                'description' => 'Klasyczny karabinek lever-action w kalibrze .357 Magnum / .38 Special. Opisy rynkowe wskazują inspirację konstrukcjami typu Winchester 1892 i typowe przyrządy “buckhorn”.',
            ],

            [
                'name' => 'Freedom Ordnance FX-9 (9mm)',
                'type' => 'Karabin',
                'caliber' => '9x19',
                'description' => 'Modułowa platforma 9 mm projektowana pod szeroką kompatybilność z akcesoriami w standardzie AR. Producent podkreśla modularność i łatwe konfigurowanie wariantów.',
            ],
        ];

        foreach ($guns as $gun) {
            $type = $types[$gun['type']];
            $caliber = $calibers[$gun['caliber']];

            Gun::updateOrCreate(
                ['name' => $gun['name']],
                [
                    'gun_type_id' => $type->id,
                    'caliber_id' => $caliber->id,
                    'description' => $gun['description'],
                    'photos' => [],
                ],
            );
        }

        $gunModelsByName = Gun::query()
            ->whereIn('name', collect($guns)->pluck('name')->all())
            ->get()
            ->keyBy('name');

        $ammunitionModelsByName = Ammunition::query()
            ->whereIn('name', collect($ammo)->pluck('name')->all())
            ->get()
            ->keyBy('name');

        $packages = [
            [
                'name' => 'Pakiet Military USA',
                'description' => 'Klasyczny zestaw platform AR i strzelby bojowej inspirowany konfiguracjami używanymi w USA.',
                'items' => [
                    ['gun' => 'BURGU BRG55 (AR-15)', 'ammo' => '5.56x45 / .223 Rem', 'shots' => 10],
                    ['gun' => 'Diamondback DB15 (AR-15)', 'ammo' => '5.56x45 / .223 Rem', 'shots' => 10],
                    ['gun' => 'Mossberg 500', 'ammo' => '12/70 Śrut', 'shots' => 5],
                ],
            ],
            [
                'name' => 'Pakiet Klasyka 9mm',
                'description' => 'Zestaw popularnych konstrukcji 9x19 do treningu statycznego i dynamicznego.',
                'items' => [
                    ['gun' => 'CZ 75 Shadow 1 (SP-01 Shadow)', 'ammo' => '9x19', 'shots' => 5],
                    ['gun' => 'Glock 19', 'ammo' => '9x19', 'shots' => 5],
                    ['gun' => 'Springfield Echelon', 'ammo' => '9x19', 'shots' => 5],
                ],
            ],
            [
                'name' => 'Pakiet AK i Wschód',
                'description' => 'Pakiet dla fanów platformy AK i historycznych konstrukcji wschodnich.',
                'items' => [
                    ['gun' => 'WBP Jack 556', 'ammo' => '5.56x45 / .223 Rem', 'shots' => 10],
                    ['gun' => 'WBP Mini Jack (7.62x39)', 'ammo' => '7.62x39', 'shots' => 10],
                    ['gun' => 'Mosin-Nagant (np. 91/30)', 'ammo' => '7.62x54R (Mosin)', 'shots' => 5],
                ],
            ],
            [
                'name' => 'Pakiet Sport .22 LR',
                'description' => 'Niski odrzut i wysoka powtarzalność: idealny pakiet na szkolenie i precyzję.',
                'items' => [
                    ['gun' => 'Ruger Mark IV 22/45', 'ammo' => '.22 LR', 'shots' => 10],
                    ['gun' => 'CZ 457', 'ammo' => '.22 LR', 'shots' => 10],
                    ['gun' => 'Hämmerli TAC R1 (.22 LR)', 'ammo' => '.22 LR', 'shots' => 10],
                ],
            ],
        ];

        foreach ($packages as $packageData) {
            $package = GunPackage::query()->updateOrCreate(
                ['name' => $packageData['name']],
                [
                    'description' => $packageData['description'],
                    'is_active' => true,
                ],
            );

            $package->packageGuns()->delete();

            collect($packageData['items'])->each(function (array $itemData, int $index) use ($package, $gunModelsByName, $ammunitionModelsByName): void {
                $gunId = $gunModelsByName->get($itemData['gun'])?->id;
                $ammunitionId = $ammunitionModelsByName->get($itemData['ammo'])?->id;

                if (! $gunId || ! $ammunitionId) {
                    return;
                }

                $package->packageGuns()->create([
                    'gun_id' => $gunId,
                    'ammunition_id' => $ammunitionId,
                    'shots_quantity' => max((int) $itemData['shots'], 1),
                    'sort_order' => $index + 1,
                ]);
            });
        }
    }
}
