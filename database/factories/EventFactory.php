<?php

use App\Event;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Event::class, function (Faker $faker) {
    return [
        'title' => "Example Competition",
        'subtitle' => 'A bit more information',
        'date' => Carbon    ::parse('+2 weeks'),
        'entry_fee' => 2000,
        'course' => 'Example Golf Course',
        'course_address' => '123 Example',
        'city' => 'Example City',
        'county' => 'Somerset',
        'postcode' => 'B3 411',
        'additional_information' => 'Some sample information'
    ];
});

$factory->state(Event::class, 'published', function (Faker $faker) {
    return [
        'published_at' => Carbon::parse('-1 week')
    ];
});

$factory->state(Event::class, 'unpublished', function (Faker $faker) {
    return [
        'published_at' => null
    ];
});
