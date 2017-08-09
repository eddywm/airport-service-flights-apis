<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */


$factory->define(App\User::class, function (Faker\Generator $faker) {

    return [
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName,
        'email' => $faker->unique()->safeEmail
    ];
});



$factory->define(App\Airport::class, function (Faker\Generator $faker) {


    return [
        'iataCode' => strtoupper(str_random(5)),
        'city' => ($faker->city),
        'state' => $faker->country,
    ];
});


$factory->define(App\Flight::class, function (Faker\Generator $faker) {
    $flightHours = $faker->numberBetween(1,5);
    $flightTime = new DateInterval('PT' .$flightHours. 'H');
    $arrival = $faker->dateTime;
    $depart = clone $arrival;
    $depart->sub($flightTime);

    return [
        'flightNumber' => strtoupper(str_random(3). $faker->unique()->randomNumber(5)) ,
        'arrivalAirport_id' => $faker->numberBetween(1,5),
        'arrivalDateTime' => $arrival,
        'departureAirport_id' => $faker->numberBetween(1,5),
        'departureDateTime' => $depart,
        'status' => $faker->boolean ? "ontime" : "delayed",
    ];
});

$factory->define(App\Customer::class, function (Faker\Generator $faker) {


    return [
        'firstName' => $faker->firstName,
        'lastName' => $faker->lastName

    ];
});

