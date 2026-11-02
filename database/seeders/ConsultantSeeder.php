<?php

namespace Database\Seeders;

use App\Models\Consultant;
use Illuminate\Database\Seeder;

class ConsultantSeeder extends Seeder
{
    public function run(): void
    {
        Consultant::updateOrCreate(
            ['slug' => 'times-consultant'],
            [
                'name' => 'Times Consultant',
                'company' => 'Times Consultant',
                'short_description' => 'Study abroad education consultancy providing guidance for international university admissions and study destinations.',
                'city' => 'Lahore',
                'address' => 'Lahore, Pakistan',
                'latitude' => 31.5204,
                'longitude' => 74.3587,
                'website' => 'https://timesconsultant.com/',
                'reference_url' => 'https://www.timesconsultant.com/',
                'reference_label' => 'Official Website',
                'services' => [
                    'University Selection',
                    'Admissions Guidance',
                    'Visa Guidance',
                    'Scholarship Guidance',
                    'Application Support',
                ],
                'destinations' => [
                    'United Kingdom',
                    'Canada',
                    'Australia',
                    'United States',
                    'Europe',
                ],
                'is_verified' => false,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        Consultant::updateOrCreate(
            ['slug' => 'abdullah-education-consultant'],
            [
                'name' => 'Abdullah Education Consultant',
                'company' => 'Abdullah Education Consultant',
                'short_description' => 'Education consultancy offering study abroad counselling, university selection, admissions and visa guidance.',
                'city' => 'Lahore',
                'address' => 'Johar Town, Lahore, Pakistan',
                'latitude' => 31.4697,
                'longitude' => 74.2728,
                'website' => 'https://abdullah-edu.com/',
                'reference_url' => 'https://abdullah-edu.com/top-10-consultants-in-lahore-trusted-experts-for-2026/',
                'reference_label' => 'Public Reference Article',
                'services' => [
                    'Educational Counselling',
                    'University Selection',
                    'Admissions Guidance',
                    'Visa Guidance',
                    'Profile Assessment',
                ],
                'destinations' => [
                    'United Kingdom',
                    'Sweden',
                    'Malaysia',
                    'Europe',
                ],
                'is_verified' => false,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        Consultant::updateOrCreate(
            ['slug' => 'vision-international-education'],
            [
                'name' => 'Vision International Education',
                'company' => 'Vision International Education',
                'short_description' => 'International education consultancy offering study abroad counselling, admissions assistance and visa support.',
                'city' => 'Lahore',
                'address' => 'Lahore, Pakistan',
                'latitude' => 31.5204,
                'longitude' => 74.3587,
                'website' => 'https://www.visioninternationaledu.com/',
                'reference_url' => 'https://www.visioninternationaledu.com/',
                'reference_label' => 'Official Website',
                'services' => [
                    'Study Abroad Counselling',
                    'University Admissions',
                    'Visa Assistance',
                    'Test Preparation',
                ],
                'destinations' => [
                    'United Kingdom',
                    'Canada',
                    'Australia',
                    'United States',
                    'Europe',
                ],
                'is_verified' => false,
                'is_featured' => true,
                'is_active' => true,
            ]
        );

        Consultant::updateOrCreate(
            ['slug' => 'global-study-partners'],
            [
                'name' => 'Global Study Partners',
                'company' => 'Global Study Partners',
                'short_description' => 'International education platform connecting students with study opportunities and education providers.',
                'city' => 'Lahore',
                'address' => 'Lahore, Pakistan',
                'latitude' => 31.5204,
                'longitude' => 74.3587,
                'website' => 'https://www.globalstudypartners.com/',
                'reference_url' => 'https://www.brighton.ac.uk/international/study-in-your-country/pakistan.aspx',
                'reference_label' => 'University Reference',
                'services' => [
                    'Study Abroad Guidance',
                    'Course Selection',
                    'University Search',
                    'Admissions Support',
                ],
                'destinations' => [
                    'United Kingdom',
                    'Australia',
                    'Canada',
                    'United States',
                    'New Zealand',
                ],
                'is_verified' => false,
                'is_featured' => false,
                'is_active' => true,
            ]
        );

        Consultant::updateOrCreate(
            ['slug' => 'idp-education-pakistan'],
            [
                'name' => 'IDP Education',
                'company' => 'IDP Education',
                'short_description' => 'International education organisation providing study abroad counselling, university application support and English language testing services.',
                'city' => 'Lahore',
                'address' => 'Lahore, Pakistan',
                'latitude' => 31.5204,
                'longitude' => 74.3587,
                'website' => 'https://www.idp.com/pakistan/',
                'reference_url' => 'https://www.brighton.ac.uk/international/study-in-your-country/pakistan.aspx',
                'reference_label' => 'University Reference',
                'services' => [
                    'Study Abroad Counselling',
                    'University Applications',
                    'Admissions Support',
                    'IELTS',
                    'Visa Guidance',
                ],
                'destinations' => [
                    'United Kingdom',
                    'Australia',
                    'Canada',
                    'United States',
                    'New Zealand',
                ],
                'is_verified' => false,
                'is_featured' => false,
                'is_active' => true,
            ]
        );
    }
}