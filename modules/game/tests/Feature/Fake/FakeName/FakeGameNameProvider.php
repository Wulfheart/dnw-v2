<?php

namespace Dnw\Game\Test\Feature\Fake\FakeName;

final class FakeGameNameProvider
{
    public static function name(): string
    {
        $themes = [
            'historisch' => [
                'Vertrag von {ort}', 'Kongress von {ort}', 'Pakt von {ort}', 'Krieg von {ort}',
                'Belagerung von {ort}', 'Schlacht von {ort}', 'Krise in {ort}', '{ort} Abkommen',
                '{ort} Diplomatie',
            ],
            'geopolitisch' => [
                '{ereignis} in {ort}', '{ort} Verhandlungen', 'Spannungen in {ort}', '{ort} Vereinbarung',
                '{ort} Gipfel', '{ort} Allianz', 'Diplomatischer Wandel in {ort}', '{ort} Bündnis',
            ],
            'diplomatisch' => [
                'Der {begriff} von {ort}', '{begriff} in {ort}', 'Der {begriff} des {epoche}', '{begriff} in {ort}',
                'Das {begriff} Abkommen', '{begriff} Verhandlungen', '{begriff} der Diplomatie',
            ],
            'strategisch' => [
                '{ereignis} von {ort}', '{ort} Konflikt', '{ort} Krieg', '{begriff} in {ort}',
                'Die {begriff} Kampagne', 'Die {ort} Kampagne', '{begriff} Offensive',
            ],
        ];

        $orte = ['Wien', 'Versailles', 'Genf', 'Berlin', 'Jalta', 'Den Haag', 'Brüssel', 'Konstantinopel',
            'Kairo', 'Madrid', 'Moskau', 'Stockholm', 'Washington', 'London', 'Paris', 'Zürich', 'Oslo'];
        $begriffe = ['Pakt', 'Vertrag', 'Abkommen', 'Gipfel', 'Krise', 'Allianz', 'Diplomatie', 'Konflikt',
            'Verhandlung', 'Schlacht', 'Kampagne', 'Offensive', 'Vorfall', 'Reform', 'Regime', 'Kampf'];
        $ereignisse = ['Krise', 'Gipfel', 'Revolution', 'Aufstand', 'Reform', 'Konfrontation', 'Entente', 'Bündnis'];
        $epochen = ['Kalter Krieg', 'Weltkrieg', 'Großer Krieg', 'Mittelalter', 'Renaissance', 'Industrielle Revolution', 'Moderne'];

        // Zufälligen Thema-Typ auswählen
        $ausgewaehltesThema = array_rand($themes);
        $format = $themes[$ausgewaehltesThema][array_rand($themes[$ausgewaehltesThema])];

        // Zufällige Begriffe ersetzen
        $ort = $orte[array_rand($orte)];
        $begriff = $begriffe[array_rand($begriffe)];
        $ereignis = $ereignisse[array_rand($ereignisse)];
        $epoche = $epochen[array_rand($epochen)];

        return str_replace(
            ['{ort}', '{begriff}', '{ereignis}', '{epoche}'],
            [$ort, $begriff, $ereignis, $epoche],
            $format
        );

    }
}
