<?php


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory(App\Airport::class, 100)->create();
       factory(App\Flight::class, 15)->create()->each(function($flight) {
           factory(App\Customer::class, 100)->make()->each(function($customer) use($flight) {
               $flight->customers()->save($customer);
           });
       });
    }
}
