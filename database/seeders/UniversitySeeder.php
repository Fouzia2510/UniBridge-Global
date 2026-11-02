<?php

namespace Database\Seeders;

use App\Models\University;
use Illuminate\Database\Seeder;

class UniversitySeeder extends Seeder
{
    public function run(): void
    {
        $universities = [

            [
                'name' => 'University of Manchester',
                'country' => 'UK',
                'city' => 'Manchester',
                'degree_level' => 'Masters',
                'subject' => 'Computer Science',
                'tuition_fee' => 28000,
                'estimated_living_cost' => 12000,
                'min_cgpa' => 3.0,
                'min_english_score' => 6.5,
                'english_test_type' => 'IELTS',
                'intake' => 'September 2026',
                'ranking' => 34,
                'scholarship_available' => true,
                'post_study_work' => true,
                'description' => 'A leading UK university offering advanced programs in Computer Science and related fields.',

                'website_url' => 'https://www.manchester.ac.uk/',
                'application_url' => 'https://www.manchester.ac.uk/study/masters/courses/',
                'contact_email' => 'international@manchester.ac.uk',
                'contact_phone' => '+44 161 306 6000',
            ],

            [
                'name' => 'University of Birmingham',
                'country' => 'UK',
                'city' => 'Birmingham',
                'degree_level' => 'Masters',
                'subject' => 'Computer Science',
                'tuition_fee' => 26500,
                'estimated_living_cost' => 11000,
                'min_cgpa' => 3.0,
                'min_english_score' => 6.5,
                'english_test_type' => 'IELTS',
                'intake' => 'September 2026',
                'ranking' => 80,
                'scholarship_available' => true,
                'post_study_work' => true,
                'description' => 'A prestigious UK university with strong programs in technology and computer science.',

                'website_url' => 'https://www.birmingham.ac.uk/',
                'application_url' => 'https://www.birmingham.ac.uk/study/postgraduate/apply',
                'contact_email' => 'international@contacts.bham.ac.uk',
                'contact_phone' => '+44 121 414 3344',
            ],

            [
                'name' => 'University of Toronto',
                'country' => 'Canada',
                'city' => 'Toronto',
                'degree_level' => 'Masters',
                'subject' => 'Computer Science',
                'tuition_fee' => 45000,
                'estimated_living_cost' => 18000,
                'min_cgpa' => 3.3,
                'min_english_score' => 7.0,
                'english_test_type' => 'IELTS',
                'intake' => 'September 2026',
                'ranking' => 21,
                'scholarship_available' => true,
                'post_study_work' => true,
                'description' => 'One of Canada’s leading research universities with excellent Computer Science programs.',

                'website_url' => 'https://www.utoronto.ca/',
                'application_url' => 'https://www.sgs.utoronto.ca/admissions/',
                'contact_email' => 'ask@utoronto.ca',
                'contact_phone' => '+1 416 978 2011',
            ],

            [
                'name' => 'University of Melbourne',
                'country' => 'Australia',
                'city' => 'Melbourne',
                'degree_level' => 'Masters',
                'subject' => 'Information Technology',
                'tuition_fee' => 42000,
                'estimated_living_cost' => 22000,
                'min_cgpa' => 3.0,
                'min_english_score' => 6.5,
                'english_test_type' => 'IELTS',
                'intake' => 'February 2027',
                'ranking' => 14,
                'scholarship_available' => true,
                'post_study_work' => true,
                'description' => 'A globally recognized Australian university offering technology and IT programs.',

                'website_url' => 'https://www.unimelb.edu.au/',
                'application_url' => 'https://study.unimelb.edu.au/how-to-apply',
                'contact_email' => 'enquiries@unimelb.edu.au',
                'contact_phone' => '+61 3 8344 4000',
            ],

            [
                'name' => 'University of Auckland',
                'country' => 'New Zealand',
                'city' => 'Auckland',
                'degree_level' => 'Bachelors',
                'subject' => 'Computer Science',
                'tuition_fee' => 36000,
                'estimated_living_cost' => 17000,
                'min_cgpa' => 2.8,
                'min_english_score' => 6.0,
                'english_test_type' => 'IELTS',
                'intake' => 'February 2027',
                'ranking' => 65,
                'scholarship_available' => true,
                'post_study_work' => true,
                'description' => 'New Zealand’s leading university with a wide range of computing and technology programs.',

                'website_url' => 'https://www.auckland.ac.nz/',
                'application_url' => 'https://www.auckland.ac.nz/en/study/how-to-apply.html',
                'contact_email' => 'studentinfo@auckland.ac.nz',
                'contact_phone' => '+64 9 923 5025',
            ],
        ];

        foreach ($universities as $university) {
            University::updateOrCreate(
                ['name' => $university['name']],
                $university
            );
        }
    }
}