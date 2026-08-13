<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Bangladesh Geo Hierarchy Configuration
    |--------------------------------------------------------------------------
    |
    | Maps all 8 Divisions, 64 Districts, and major Tourist Upazilas/Areas
    | for hyper-local search filtering on Prime Booking.
    |
    */
    'divisions' => [
        'Chittagong' => [
            'name' => 'Chittagong (চট্টগ্রাম)',
            'districts' => [
                "Cox's Bazar" => [
                    'name' => "Cox's Bazar (কক্সবাজার)",
                    'upazilas' => ["Kolatoli Beach", "Inani Beach", "Teknaf", "Saint Martin's Island", "Marine Drive", "Ramuk"],
                ],
                'Chittagong' => [
                    'name' => 'Chittagong (চট্টগ্রাম শহর)',
                    'upazilas' => ['Panchlaish', 'GEC Circle', 'Agrabad', 'Patenga Beach', 'Halishahar'],
                ],
                'Rangamati' => [
                    'name' => 'Rangamati (রাঙ্গামাটি)',
                    'upazilas' => ['Sajek Valley', 'Ruilui Para', 'Kaptai', 'Sadar'],
                ],
                'Bandarban' => [
                    'name' => 'Bandarban (বান্দরবান)',
                    'upazilas' => ['Nilgiri', 'Thanchi', 'Chimbuk', 'Bogalake', 'Sadar'],
                ],
                'Khagrachhari' => [
                    'name' => 'Khagrachhari (খাগড়াছড়ি)',
                    'upazilas' => ['Alutila', 'Sajek Road', 'Sadar'],
                ],
                'Comilla' => [
                    'name' => 'Comilla (কুমিল্লা)',
                    'upazilas' => ['Kotbari', 'Sadar'],
                ],
                'Feni' => [
                    'name' => 'Feni (ফেনী)',
                    'upazilas' => ['Sadar'],
                ],
                'Noakhali' => [
                    'name' => 'Noakhali (নোয়াখালী)',
                    'upazilas' => ['Nijhum Dwip', 'Hatiya'],
                ],
            ],
        ],
        'Sylhet' => [
            'name' => 'Sylhet (সিলেট)',
            'districts' => [
                'Sylhet' => [
                    'name' => 'Sylhet (সিলেট শহর ও আশেপাশের)',
                    'upazilas' => ['Zindabazar', 'Upashahar', 'Khadimnagar', 'Jaflong', 'Bichnakandi', 'Ratargul', 'Sadar'],
                ],
                'Moulvibazar' => [
                    'name' => 'Moulvibazar (মৌলভীবাজার & শ্রীমঙ্গল)',
                    'upazilas' => ['Sreemangal Upazila', 'Radhanagar', 'Kamalganj', 'Lawachara', 'Sadar'],
                ],
                'Sunamganj' => [
                    'name' => 'Sunamganj (সুনামগঞ্জ & টাঙ্গুয়ার হাওর)',
                    'upazilas' => ['Tanguar Haor', 'Tahirpur', 'Sadar'],
                ],
                'Habiganj' => [
                    'name' => 'Habiganj (হবিগঞ্জ)',
                    'upazilas' => ['Srimangal Road', 'Bahubal', 'Sadar'],
                ],
            ],
        ],
        'Dhaka' => [
            'name' => 'Dhaka (ঢাকা)',
            'districts' => [
                'Dhaka' => [
                    'name' => 'Dhaka City (ঢাকা শহর)',
                    'upazilas' => ['Uttara', 'Gulshan', 'Banani', 'Dhanmondi', 'Mirpur', 'Motijheel', 'Puran Dhaka'],
                ],
                'Gazipur' => [
                    'name' => 'Gazipur (গাজীপুর রিসোর্ট এলাকা)',
                    'upazilas' => ['Sreepur', 'Bhawal', 'Sadar'],
                ],
                'Narayanganj' => [
                    'name' => 'Narayanganj (নারায়ণগঞ্জ)',
                    'upazilas' => ['Sonargaon', 'Sadar'],
                ],
                'Munshiganj' => [
                    'name' => 'Munshiganj (মুন্সীগঞ্জ)',
                    'upazilas' => ['Mawa Ghat', 'Sadar'],
                ],
                'Faridpur' => [
                    'name' => 'Faridpur (ফরিদপুর)',
                    'upazilas' => ['Sadar'],
                ],
            ],
        ],
        'Khulna' => [
            'name' => 'Khulna (খুলনা & সুন্দরবন)',
            'districts' => [
                'Khulna' => [
                    'name' => 'Khulna (খুলনা শহর)',
                    'upazilas' => ['City Centre', 'Sonadanga', 'Sadar'],
                ],
                'Bagerhat' => [
                    'name' => 'Bagerhat (সুন্দরবন & মংলা)',
                    'upazilas' => ['Sundarbans', 'Mongla Port', 'Shat Gombuj', 'Sadar'],
                ],
                'Satkhira' => [
                    'name' => 'Satkhira (সাতক্ষীরা)',
                    'upazilas' => ['Sundarbans Range', 'Sadar'],
                ],
                'Jessore' => [
                    'name' => 'Jessore (যশোর)',
                    'upazilas' => ['Sadar', 'Airport Area'],
                ],
            ],
        ],
        'Barisal' => [
            'name' => 'Barisal (বরিশাল & কুয়াকাটা)',
            'districts' => [
                'Patuakhali' => [
                    'name' => 'Patuakhali (কুয়াকাটা সমুদ্র সৈকত)',
                    'upazilas' => ['Kuakata Beach', 'Kalapara', 'Sadar'],
                ],
                'Barisal' => [
                    'name' => 'Barisal (বরিশাল শহর)',
                    'upazilas' => ['Floating Market', 'Sadar'],
                ],
                'Bhola' => [
                    'name' => 'Bhola (ভোলা)',
                    'upazilas' => ['Char Fashion', 'Sadar'],
                ],
            ],
        ],
        'Rajshahi' => [
            'name' => 'Rajshahi (রাজশাহী)',
            'districts' => [
                'Rajshahi' => [
                    'name' => 'Rajshahi (রাজশাহী শহর)',
                    'upazilas' => ['Shaheb Bazar', 'Padma Garden', 'Sadar'],
                ],
                'Bogra' => [
                    'name' => 'Bogra (বগুড়া)',
                    'upazilas' => ['Mahasthangarh', 'Sadar'],
                ],
                'Pabna' => [
                    'name' => 'Pabna (পাবনা)',
                    'upazilas' => ['Sadar'],
                ],
                'Naogaon' => [
                    'name' => 'Naogaon (নওগাঁ)',
                    'upazilas' => ['Paharpur', 'Sadar'],
                ],
            ],
        ],
        'Rangpur' => [
            'name' => 'Rangpur (রংপুর)',
            'districts' => [
                'Rangpur' => [
                    'name' => 'Rangpur (রংপুর শহর)',
                    'upazilas' => ['Tajhat', 'Sadar'],
                ],
                'Dinajpur' => [
                    'name' => 'Dinajpur (দিনাজপুর)',
                    'upazilas' => ['Kantajew Temple', 'Sadar'],
                ],
                'Panchagarh' => [
                    'name' => 'Panchagarh (পঞ্চগড় & তেঁতুলিয়া)',
                    'upazilas' => ['Tetulia', 'Banglabandha', 'Sadar'],
                ],
            ],
        ],
        'Mymensingh' => [
            'name' => 'Mymensingh (ময়মনসিংহ)',
            'districts' => [
                'Mymensingh' => [
                    'name' => 'Mymensingh (ময়মনসিংহ)',
                    'upazilas' => ['Sadar', 'Muktagacha'],
                ],
                'Sherpur' => [
                    'name' => 'Sherpur (শেরপুর & গজনি)',
                    'upazilas' => ['Gajni', 'Sadar'],
                ],
                'Netrokona' => [
                    'name' => 'Netrokona (নেত্রকোণা & বিরিশিরি)',
                    'upazilas' => ['Birishiri', 'Durgapur', 'Sadar'],
                ],
            ],
        ],
    ],
];
