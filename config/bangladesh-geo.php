<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Bangladesh Geo Hierarchy Configuration (English Standard)
    |--------------------------------------------------------------------------
    |
    | Maps all 8 Divisions, 64 Districts, and major Tourist Upazilas/Areas
    | for hyper-local search filtering on Prime Booking.
    |
    */
    'divisions' => [
        'Chittagong' => [
            'name' => 'Chittagong Division',
            'districts' => [
                "Cox's Bazar" => [
                    'name' => "Cox's Bazar District",
                    'upazilas' => ["Kolatoli Beach", "Inani Beach", "Teknaf", "Saint Martin's Island", "Marine Drive", "Ramuk"],
                ],
                'Chittagong' => [
                    'name' => 'Chittagong City & Sadar',
                    'upazilas' => ['Panchlaish', 'GEC Circle', 'Agrabad', 'Patenga Beach', 'Halishahar'],
                ],
                'Rangamati' => [
                    'name' => 'Rangamati District',
                    'upazilas' => ['Sajek Valley', 'Ruilui Para', 'Kaptai', 'Sadar'],
                ],
                'Bandarban' => [
                    'name' => 'Bandarban District',
                    'upazilas' => ['Nilgiri', 'Thanchi', 'Chimbuk', 'Bogalake', 'Sadar'],
                ],
                'Khagrachhari' => [
                    'name' => 'Khagrachhari District',
                    'upazilas' => ['Alutila', 'Sajek Road', 'Sadar'],
                ],
                'Comilla' => [
                    'name' => 'Comilla District',
                    'upazilas' => ['Kotbari', 'Sadar'],
                ],
                'Feni' => [
                    'name' => 'Feni District',
                    'upazilas' => ['Sadar'],
                ],
                'Noakhali' => [
                    'name' => 'Noakhali District',
                    'upazilas' => ['Nijhum Dwip', 'Hatiya'],
                ],
            ],
        ],
        'Sylhet' => [
            'name' => 'Sylhet Division',
            'districts' => [
                'Sylhet' => [
                    'name' => 'Sylhet City & Area',
                    'upazilas' => ['Zindabazar', 'Upashahar', 'Khadimnagar', 'Jaflong', 'Bichnakandi', 'Ratargul', 'Sadar'],
                ],
                'Moulvibazar' => [
                    'name' => 'Moulvibazar & Sreemangal',
                    'upazilas' => ['Sreemangal Upazila', 'Radhanagar', 'Kamalganj', 'Lawachara', 'Sadar'],
                ],
                'Sunamganj' => [
                    'name' => 'Sunamganj & Tanguar Haor',
                    'upazilas' => ['Tanguar Haor', 'Tahirpur', 'Sadar'],
                ],
                'Habiganj' => [
                    'name' => 'Habiganj District',
                    'upazilas' => ['Srimangal Road', 'Bahubal', 'Sadar'],
                ],
            ],
        ],
        'Dhaka' => [
            'name' => 'Dhaka Division',
            'districts' => [
                'Dhaka' => [
                    'name' => 'Dhaka City Area',
                    'upazilas' => ['Uttara', 'Gulshan', 'Banani', 'Dhanmondi', 'Mirpur', 'Motijheel', 'Puran Dhaka'],
                ],
                'Gazipur' => [
                    'name' => 'Gazipur Resort Area',
                    'upazilas' => ['Sreepur', 'Bhawal', 'Sadar'],
                ],
                'Narayanganj' => [
                    'name' => 'Narayanganj District',
                    'upazilas' => ['Sonargaon', 'Sadar'],
                ],
                'Munshiganj' => [
                    'name' => 'Munshiganj District',
                    'upazilas' => ['Mawa Ghat', 'Sadar'],
                ],
                'Faridpur' => [
                    'name' => 'Faridpur District',
                    'upazilas' => ['Sadar'],
                ],
            ],
        ],
        'Khulna' => [
            'name' => 'Khulna Division',
            'districts' => [
                'Khulna' => [
                    'name' => 'Khulna City',
                    'upazilas' => ['City Centre', 'Sonadanga', 'Sadar'],
                ],
                'Bagerhat' => [
                    'name' => 'Bagerhat & Sundarbans',
                    'upazilas' => ['Sundarbans', 'Mongla Port', 'Shat Gombuj', 'Sadar'],
                ],
                'Satkhira' => [
                    'name' => 'Satkhira District',
                    'upazilas' => ['Sundarbans Range', 'Sadar'],
                ],
                'Jessore' => [
                    'name' => 'Jessore District',
                    'upazilas' => ['Sadar', 'Airport Area'],
                ],
            ],
        ],
        'Barisal' => [
            'name' => 'Barisal Division',
            'districts' => [
                'Patuakhali' => [
                    'name' => 'Patuakhali & Kuakata',
                    'upazilas' => ['Kuakata Beach', 'Kalapara', 'Sadar'],
                ],
                'Barisal' => [
                    'name' => 'Barisal City',
                    'upazilas' => ['Floating Market', 'Sadar'],
                ],
                'Bhola' => [
                    'name' => 'Bhola District',
                    'upazilas' => ['Char Fashion', 'Sadar'],
                ],
            ],
        ],
        'Rajshahi' => [
            'name' => 'Rajshahi Division',
            'districts' => [
                'Rajshahi' => [
                    'name' => 'Rajshahi City',
                    'upazilas' => ['Shaheb Bazar', 'Padma Garden', 'Sadar'],
                ],
                'Bogra' => [
                    'name' => 'Bogra District',
                    'upazilas' => ['Mahasthangarh', 'Sadar'],
                ],
                'Pabna' => [
                    'name' => 'Pabna District',
                    'upazilas' => ['Sadar'],
                ],
                'Naogaon' => [
                    'name' => 'Naogaon District',
                    'upazilas' => ['Paharpur', 'Sadar'],
                ],
            ],
        ],
        'Rangpur' => [
            'name' => 'Rangpur Division',
            'districts' => [
                'Rangpur' => [
                    'name' => 'Rangpur City',
                    'upazilas' => ['Tajhat', 'Sadar'],
                ],
                'Dinajpur' => [
                    'name' => 'Dinajpur District',
                    'upazilas' => ['Kantajew Temple', 'Sadar'],
                ],
                'Panchagarh' => [
                    'name' => 'Panchagarh & Tetulia',
                    'upazilas' => ['Tetulia', 'Banglabandha', 'Sadar'],
                ],
            ],
        ],
        'Mymensingh' => [
            'name' => 'Mymensingh Division',
            'districts' => [
                'Mymensingh' => [
                    'name' => 'Mymensingh City',
                    'upazilas' => ['Sadar', 'Muktagacha'],
                ],
                'Sherpur' => [
                    'name' => 'Sherpur & Gajni',
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
