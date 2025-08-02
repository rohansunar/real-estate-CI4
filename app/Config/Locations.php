<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Locations extends BaseConfig
{
    /**
     * Available locations for properties (Siliguri and nearby areas)
     *
     * @var array
     */
    public array $locations = [
        'Siliguri',
        'Champasari',
        'Bagdogra',
        'Jalpaiguri',
        'Pradhan Nagar',
        'Milan More',
        'Khaprail',
        'Matigara',
        'Sukna',
        'Sevoke',
        'Phansidewa',
        'Naxalbari',
        'Kurseong',
        'Kalimpong',
        'Darjeeling'
    ];

    /**
     * Property types
     *
     * @var array
     */
    public array $propertyTypes = [
        'house' => 'House',
        'apartment' => 'Apartment',
        'villa' => 'Villa',
        'land' => 'Land'
    ];
}
