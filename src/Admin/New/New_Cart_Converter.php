<?php

namespace Tigon\DmsConnect\Admin\New;

use WP_Error;

class New_Cart_Converter
{
    static $locations = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12', 'T13'];

    static $denago_colors = [
        'Black',
        'Blue',
        'Champagne',
        'Gray',
        'Lava',
        'White',
        'Verdant'
    ];

    static $epic_colors = [
        'Black',
        'Charcoal Gray',
        'Dark Blue',
        'Light Blue',
        'Matte Black',
        'Red Pearl',
        'Silver',
        'White',
        'White Pearl'
    ];

    static $evolution_colors = [
        'Artic grey',
        'Black',
        'Black sapphire',
        'Blue',
        'Candy apple',
        'Copper',
        'Flamenco red',
        'Green',
        'Lime green',
        'Mediterranean blue',
        'Midnight blue',
        'Mineral white',
        'Navy blue',
        'Portimao blue',
        'Red',
        'Silver',
        'Sky blue',
        'White'
    ];

    static $icon_colors = [
        'Black',
        'Caribbean',
        'Champagne',
        'Forest',
        'Indigo',
        'Lime',
        'Orange',
        'Purple',
        'Sangria',
        'Silver',
        'Torch',
        'White',
        'Yellow'
    ];

    static $swift_colors = [
        'Black',
        'Blue',
        'Champagne',
        'Green',
        'Grey',
        'Lime',
        'Orange',
        'Pink',
        'Purple',
        'Red',
        'Silver',
        'Sky Blue',
        'White',
        'Yellow'
    ];

    private $new_carts;

    function __construct()
    {
        $this->new_carts = self::generate_new_carts();
    }

    public static function generate_new_carts()
    {
        $models = [
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Champagne',
                    'Gray',
                    'Lava',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Denago',
                    'model' => 'Nomad'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Stone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Denago',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Gray',
                    'Lava',
                    'White',
                    'Verdant'
                ],
                'cartType' => [
                    'make' => 'Denago',
                    'model' => 'Nomad XL'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Stone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Denago',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Gray',
                    'Lava',
                    'White',
                    'Verdant'
                ],
                'cartType' => [
                    'make' => 'Denago',
                    'model' => 'Rover XL'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Stone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Denago',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '9995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Charcoal Gray',
                    'Dark Blue',
                    'Light Blue',
                    'Matte Black',
                    'Red Pearl',
                    'Silver',
                    'White Pearl'
                ],
                'cartType' => [
                    'make' => 'Epic',
                    'model' => 'E40L'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Leoch',
                    'type' => 'AGM',
                    'ampHours' => '210',
                    'packVoltage' => '36',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '14500',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Charcoal Gray',
                    'Dark Blue',
                    'Light Blue',
                    'Matte Black',
                    'Red Pearl',
                    'Silver',
                    'White Pearl'
                ],
                'cartType' => [
                    'make' => 'Epic',
                    'model' => 'E60'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '12',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Leoch',
                    'type' => 'AGM',
                    'ampHours' => '210',
                    'packVoltage' => '36',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '14500',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Charcoal Gray',
                    'Dark Blue',
                    'Light Blue',
                    'Matte Black',
                    'Red Pearl',
                    'Silver',
                    'White Pearl'
                ],
                'cartType' => [
                    'make' => 'Epic',
                    'model' => 'E60L'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Leoch',
                    'type' => 'AGM',
                    'ampHours' => '210',
                    'packVoltage' => '36',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '15500',
                'warrantyLength' => '3'
            ],
            [
                'colors' => ['Mineral white'],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'Carrier 6 PLUS'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '9995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mineral white',
                    'Sky blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'CLASSIC 2 PLUS'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '6795',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Candy apple',
                    'Lime green',
                    'Navy blue',
                    'Red',
                    'Silver',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'CLASSIC 2 PRO'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '6995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mineral white',
                    'Sky blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'CLASSIC 4 PLUS'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '6995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Candy apple',
                    'Lime green',
                    'Navy blue',
                    'Red',
                    'Silver',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'CLASSIC 4 PRO'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '6995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mediterranean blue',
                    'Mineral white',
                    'Portimao blue',
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D3'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '17500',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mediterranean blue',
                    'Mineral white',
                    'Sky blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D5 Maverick 2+2'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '8995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mediterranean blue',
                    'Mineral white',
                    'Portimao blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D5 MAVERICK 6'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '12995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Lime green',
                    'Mediterranean blue',
                    'Mineral white',
                    'Portimao blue',
                    'Sky blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D5 Ranger 2+2'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '8595',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Mineral white'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D5-C6 RANGER'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '11995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mediterranean blue',
                    'Mineral white'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D5-F4 MAVERICK'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '9995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Mineral white',
                    'Mediterranean Blue',
                    'Blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'Forester 4+'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Mineral white'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'Forester 6+'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => true,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '10995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'Turfman 200'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => true,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '7995',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Artic grey',
                    'Black sapphire',
                    'Flamenco red',
                    'Lime green',
                    'Mediterranean blue',
                    'Mineral white',
                    'Portimao blue',
                    'Sky blue'
                ],
                'cartType' => [
                    'make' => 'Evolution',
                    'model' => 'D5-C4 RANGER'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => true,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'HDK',
                    'type' => 'Lithium',
                    'ampHours' => '110',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '9595',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C20S'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '9495',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C20U'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '12995',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C20UL'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '23000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C20V'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '16560',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C30AMB'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '17460',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C30AMBL'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '18995',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C40'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '8000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C40FLS'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '11429',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C40FS'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '14669',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C40L'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '9000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C60'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '11069',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C60FS'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '14669',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C60L'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '11519',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C70W'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '18900',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'C80'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '8 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => false
                ],
                'retailPrice' => '16050',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Forest',
                    'Indigo',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'G40'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => false,
                'battery' => [
                    'brand' => null,
                    'type' => null,
                    'ampHours' => null,
                    'packVoltage' => null,
                    'warrantyLength' => null,
                    'isDC' => null
                ],
                'engine' => [
                    'make' => 'Icon EFIA03 EFI',
                    'horsepower' => '13.5',
                    'stroke' => '4'
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '10498',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Forest',
                    'Indigo',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'G40L'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => false,
                'battery' => [
                    'brand' => null,
                    'type' => null,
                    'ampHours' => null,
                    'packVoltage' => null,
                    'warrantyLength' => null,
                    'isDC' => null
                ],
                'engine' => [
                    'make' => 'Icon EFIA03 EFI',
                    'horsepower' => '13.5',
                    'stroke' => '4'
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '11998',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Forest',
                    'Indigo',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'G60'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '12',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => false,
                'battery' => [
                    'brand' => null,
                    'type' => null,
                    'ampHours' => null,
                    'packVoltage' => null,
                    'warrantyLength' => null,
                    'isDC' => null
                ],
                'engine' => [
                    'make' => 'Icon EFIA03 EFI',
                    'horsepower' => '13.5',
                    'stroke' => '4'
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '12999',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Forest',
                    'Indigo',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'G60L'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Black',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => false,
                'battery' => [
                    'brand' => null,
                    'type' => null,
                    'ampHours' => null,
                    'packVoltage' => null,
                    'warrantyLength' => null,
                    'isDC' => null
                ],
                'engine' => [
                    'make' => 'Icon EFIA03 EFI',
                    'horsepower' => '13.5',
                    'stroke' => '4'
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '13999',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i20'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i20L'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '11000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i20S-HD'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '15599',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i20U-HD'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '11900',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i20UL-HD'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '2 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '20299',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '6500',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Champagne',
                    'Forest',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40-ECO'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '8',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7999',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40F'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '12298',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40FL'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '13498',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40FS-HD'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '4 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '21599',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40L'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Champagne',
                    'Forest',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i40L-ECO'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '8999',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i60'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '10000',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i60-HD'
                ],
                'cartAttributes' => [
                    'seatColor' => 'Brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '20599',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'White'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i60FS-HD'
                ],
                'cartAttributes' => [
                    'seatColor' => 'brown',
                    'tireRimSize' => '10',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => false,
                    'passengers' => '6 Passenger',
                    'utilityBed' => true
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '22599',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Caribbean',
                    'Champagne',
                    'Forest',
                    'Indigo',
                    'Lime',
                    'Orange',
                    'Purple',
                    'Sangria',
                    'Silver',
                    'Torch',
                    'White',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Icon',
                    'model' => 'i60L'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'Icon',
                    'type' => 'AGM',
                    'ampHours' => '165',
                    'packVoltage' => '48',
                    'warrantyLength' => '2',
                    'isDC' => ''
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '10500',
                'warrantyLength' => '3'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Champagne',
                    'Red',
                    'White'
                ],
                'cartType' => [
                    'make' => 'Swift',
                    'model' => 'Mach 4'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'ECO',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '11500',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Champagne',
                    'Red',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Swift',
                    'model' => 'Mach 4E'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '4 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'ECO',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '7500',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Champagne',
                    'Red',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Swift',
                    'model' => 'Mach 6'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '14',
                    'tireType' => 'All-Terrain',
                    'hasSoundSystem' => false,
                    'isLifted' => true,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'ECO',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '14000',
                'warrantyLength' => '2'
            ],
            [
                'colors' => [
                    'Black',
                    'Blue',
                    'Champagne',
                    'Red',
                    'Yellow'
                ],
                'cartType' => [
                    'make' => 'Swift',
                    'model' => 'Mach 6E'
                ],
                'cartAttributes' => [
                    'seatColor' => '2 Tone',
                    'tireRimSize' => '12',
                    'tireType' => 'Street Tire',
                    'hasSoundSystem' => false,
                    'isLifted' => false,
                    'hasHitch' => false,
                    'hasExtendedTop' => true,
                    'passengers' => '6 Passenger'
                ],
                'isElectric' => true,
                'battery' => [
                    'brand' => 'ECO',
                    'type' => 'Lithium',
                    'ampHours' => '105',
                    'packVoltage' => '48',
                    'warrantyLength' => '5',
                    'isDC' => false
                ],
                'engine' => [
                    'make' => null,
                    'horsepower' => null,
                    'stroke' => null
                ],
                'title' => [
                    'isStreetLegal' => true
                ],
                'retailPrice' => '11500',
                'warrantyLength' => '2'
            ]
        ];

        $model_structure = [
            'cartType' => [
                'make' => null,
                'model' => null,
                'year' => null
            ],
            'cartAttributes' => [
                'cartColor' => null,
                'seatColor' => null,
                'tireRimSize' => null,
                'tireType' => null,
                'hasSoundSystem' => null,
                'isLifted' => null,
                'hasHitch' => null,
                'hasExtendedTop' => null,
                'passengers' => null
            ],
            'battery' => [
                'year' => null,
                'brand' => null,
                'type' => null,
                'serialNo' => null,
                'ampHours' => null,
                'batteryVoltage' => null,
                'packVoltage' => null,
                'warrantyLength' => null,
                'isDC' => null
            ],
            'engine' => [
                'make' => null,
                'horsepower' => null,
                'stroke' => null
            ],
            'cartLocation' => ['locationId' => null],
            'title' => [
                'isStreetLegal' => null,
            ],
            'retailPrice' => null,
            'isElectric' => null,
            'warrantyLength' => null,
            'isUsed' => false,
            'imageUrls' => [],
            'advertising' => [
                'websiteUrl' => null,
                'needOnWebsite' => true
            ]
        ];

        $processed_carts = [];

        $count = 0;
        foreach ($models as $model) {
            $populated_cart = array_replace_recursive($model_structure, $model);
            
            $populated_cart['advertising']['cartAddOns'] = ['Standard Add Ons'];
            $populated_cart['addedFeatures']['stockOptions'] = true;

            unset($populated_cart['colors']);

            //array_push($populated_cart['imageUrls'], ['placeholder']);

            array_push($processed_carts, $populated_cart);
        }

        return $processed_carts;
    }

    public function get_all()
    {
        echo $this->new_carts;
        exit;
    }

    public function get_specific($model)
    {
        $normalized_requested_model = self::normalize_md_key($model);

        foreach ($this->new_carts as $cart) {
            if (self::normalize_md_key($cart['cartType']['model']) == $normalized_requested_model) {
                return $cart;
            }

            $make_model_key = self::normalize_md_key($cart['cartType']['make'] . ' ' . $cart['cartType']['model']);
            if ($make_model_key == $normalized_requested_model) {
                return $cart;
            }
        }
        return new WP_Error(400, 'Specified cart not in list.', "$model");
    }

    private static function normalize_md_key($value)
    {
        $normalized = strtoupper(trim((string) $value));
        $normalized = str_replace(['®', 'EV', '_'], ['', '', ' '], $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        $normalized = str_replace(['EZ GO', 'EZ-GO'], 'EZGO', $normalized);
        $normalized = str_replace(['DRIVE 2'], 'DRIVE2', $normalized);

        return trim($normalized);
    }
}
