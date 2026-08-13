<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Bangladesh Geo Hierarchy Configuration (Clean City / Region Names)
    |--------------------------------------------------------------------------
    |
    | Maps main regions (Dhaka, Sylhet, Chittagong, Cox's Bazar, etc.),
    | key Districts, and Tourist Upazilas/Areas.
    |
    */
    'divisions' => [
        'Dhaka' => [
            'name' => 'Dhaka',
            'districts' => [
                'Dhaka' => [
                    'name' => 'Dhaka City',
                    'upazilas' => ['Uttara', 'Gulshan', 'Banani', 'Dhanmondi', 'Mirpur', 'Motijheel', 'Puran Dhaka'],
                ],
                'Gazipur' => [
                    'name' => 'Gazipur Resort Area',
                    'upazilas' => ['Sreepur', 'Bhawal', 'Sadar'],
                ],
                'Narayanganj' => [
                    'name' => 'Narayanganj',
                    'upazilas' => ['Sonargaon', 'Sadar'],
                ],
                'Munshiganj' => [
                    'name' => 'Munshiganj',
                    'upazilas' => ['Mawa Ghat', 'Sadar'],
                ],
                'Faridpur' => [
                    'name' => 'Faridpur',
                    'upazilas' => ['Sadar'],
                ],
            ],
        ],
        'Sylhet' => [
            'name' => 'Sylhet',
            'districts' => [
                'Sylhet' => [
                    'name' => 'Sylhet City',
                    'upazilas' => ['Zindabazar', 'Upashahar', 'Khadimnagar', 'Jaflong', 'Bichnakandi', 'Ratargul', 'Sadar'],
                ],
                'Moulvibazar' => [
                    'name' => 'Sreemangal & Moulvibazar',
                    'upazilas' => ['Sreemangal Upazila', 'Radhanagar', 'Kamalganj', 'Lawachara', 'Sadar'],
                ],
                'Sunamganj' => [
                    'name' => 'Sunamganj & Tanguar Haor',
                    'upazilas' => ['Tanguar Haor', 'Tahirpur', 'Sadar'],
                ],
                'Habiganj' => [
                    'name' => 'Habiganj',
                    'upazilas' => ['Srimangal Road', 'Bahubal', 'Sadar'],
                ],
            ],
        ],
        'Chittagong' => [
            'name' => 'Chittagong & Cox\'s Bazar',
            'districts' => [
                "Cox's Bazar" => [
                    'name' => "Cox's Bazar",
                    'upazilas' => ["Kolatoli Beach", "Inani Beach", "Teknaf", "Saint Martin's Island", "Marine Drive", "Ramuk"],
                ],
                'Chittagong' => [
                    'name' => 'Chittagong City',
                    'upazilas' => ['Panchlaish', 'GEC Circle', 'Agrabad', 'Patenga Beach', 'Halishahar'],
                ],
                'Rangamati' => [
                    'name' => 'Rangamati & Sajek',
                    'upazilas' => ['Sajek Valley', 'Ruilui Para', 'Kaptai', 'Sadar'],
                ],
                'Bandarban' => [
                    'name' => 'Bandarban',
                    'upazilas' => ['Nilgiri', 'Thanchi', 'Chimbuk', 'Bogalake', 'Sadar'],
                ],
                'Khagrachhari' => [
                    'name' => 'Khagrachhari',
                    'upazilas' => ['Alutila', 'Sajek Road', 'Sadar'],
                ],
                'Comilla' => [
                    'name' => 'Comilla',
                    'upazilas' => ['Kotbari', 'Sadar'],
                ],
                'Feni' => [
                    'name' => 'Feni',
                    'upazilas' => ['Sadar'],
                ],
                'Noakhali' => [
                    'name' => 'Noakhali',
                    'upazilas' => ['Nijhum Dwip', 'Hatiya'],
                ],
            ],
        ],
        'Khulna' => [
            'name' => 'Khulna & Sundarbans',
            'districts' => [
                'Bagerhat' => [
                    'name' => 'Sundarbans & Mongla',
                    'upazilas' => ['Sundarbans', 'Mongla Port', 'Shat Gombuj', 'Sadar'],
                ],
                'Khulna' => [
                    'name' => 'Khulna City',
                    'upazilas' => ['City Centre', 'Sonadanga', 'Sadar'],
                ],
                'Satkhira' => [
                    'name' => 'Satkhira',
                    'upazilas' => ['Sundarbans Range', 'Sadar'],
                ],
                'Jessore' => [
                    'name' => 'Jessore',
                    'upazilas' => ['Sadar', 'Airport Area'],
                ],
            ],
        ],
        'Barisal' => [
            'name' => 'Barisal & Kuakata',
            'districts' => [
                'Patuakhali' => [
                    'name' => 'Kuakata Beach',
                    'upazilas' => ['Kuakata Beach', 'Kalapara', 'Sadar'],
                ],
                'Barisal' => [
                    'name' => 'Barisal City',
                    'upazilas' => ['Floating Market', 'Sadar'],
                ],
                'Bhola' => [
                    'name' => 'Bhola',
                    'upazilas' => ['Char Fashion', 'Sadar'],
                ],
            ],
        ],
        'Rajshahi' => [
            'name' => 'Rajshahi',
            'districts' => [
                'Rajshahi' => [
                    'name' => 'Rajshahi City',
                    'upazilas' => ['Shaheb Bazar', 'Padma Garden', 'Sadar'],
                ],
                'Bogra' => [
                    'name' => 'Bogra',
                    'upazilas' => ['Mahasthangarh', 'Sadar'],
                ],
                'Pabna' => [
                    'name' => 'Pabna',
                    'upazilas' => ['Sadar'],
                ],
                'Naogaon' => [
                    'name' => 'Naogaon',
                    'upazilas' => ['Paharpur', 'Sadar'],
                ],
            ],
        ],
        'Rangpur' => [
            'name' => 'Rangpur',
            'districts' => [
                'Rangpur' => [
                    'name' => 'Rangpur City',
                    'upazilas' => ['Tajhat', 'Sadar'],
                ],
                'Dinajpur' => [
                    'name' => 'Dinajpur',
                    'upazilas' => ['Kantajew Temple', 'Sadar'],
                ],
                'Panchagarh' => [
                    'name' => 'Panchagarh & Tetulia',
                    'upazilas' => ['Tetulia', 'Banglabandha', 'Sadar'],
                ],
            ],
        ],
        'Mymensingh' => [
            'name' => 'Mymensingh',
            'districts' => [
                'Mymensingh' => [
                    'name' => 'Mymensingh City',
                    'upazilas' => ['Sadar', 'Muktagacha'],
                ],
                'Sherpur' => [
                    'name' => 'Sherpur',
                    'upazilas' => ['Gajni', 'Sadar'],
                ],
                'Netrokona' => [
                    'name' => 'Netrokona & Birishiri',
                    'upazilas' => ['Birishiri', 'Durgapur', 'Sadar'],
                ],
            ],
        ],
    ],
];
