<?php

/*
=============================================================
File name   : FlightService.php.
Author      : Eddy WM [ eddywmdev@gmail.com ]
Date        : 7/31/17 11:15 PM
Description : Code written for ........
=============================================================
*/

namespace App\Services\v1;

use App\Airport;
use App\Flight;
use Illuminate\Support\Facades\Validator;

class FlightService
{
    protected $supportedIncludes = [
        'arrivalAirport' => 'arrival',
        'departureAirport' => 'departure'
    ];

    protected $clauseProperties = [
        'status', 'flightNumber'
    ];
    protected $roules = [
        'flightNumber' => 'required',
        'status' => 'required|flightStatus',
        'arrival.datetime' => 'required|date',
        'arrival.iataCode' => 'required',
        'departure.datetime' => 'required|date',
        'departure.iataCode' => 'required'
    ];

    public function getFlights($parameters){
        if(empty($parameters)){
            return $this->filterFlights(Flight::all()) ;
        }

        $withKeys = $this->getWithKeys($parameters);
        $whereClauses = $this->getWhereClause($parameters);

        $flights = Flight::with($withKeys)->where($whereClauses)->get();



        return $this->filterFlights($flights, $withKeys) ;
    }

    public function getFlight($flightNumber){

        return $this->filterFlights(Flight::where('flightNUmber', $flightNumber)->get()) ;
    }


    public function createFlight($request){

        $this->validate($request->all());

        $arrivalAirport = $request->input('arrival.iataCode');
        $departureAirport = $request->input('departure.iataCode');

        $airports = Airport::whereIn('iataCode', [
            $arrivalAirport, $departureAirport
        ])->get();

        $codes = [];

        foreach ($airports as $airport){
            $codes[$airport->iataCode] = $airport->id;
        }

        $flight = new Flight();
        $flight->flightNumber = $request->input('flightNumber');
        $flight->status = $request->input('status');
        $flight->arrivalAirport_id = $codes[$arrivalAirport];
        $flight->arrivalDateTime = $request->input('arrival.datetime');
        $flight->departureAirport_id = $codes[$departureAirport];
        $flight->departureDateTime = $request->input('departure.datetime');

        $flight->save();

        return $this->filterFlights([$flight]);
    }


    public function updateFlight($request, $flightNumber){
        $this->validate($request->all());

        $flight = Flight::where('flightNumber', $flightNumber)->firstOrFail();

        $arrivalAirport = $request->input('arrival.iataCode');
        $departureAirport = $request->input('departure.iataCode');

        $airports = Airport::whereIn('iataCode', [
            $arrivalAirport, $departureAirport
        ])->get();

        $codes = [];

        foreach ($airports as $airport){
            $codes[$airport->iataCode] = $airport->id;
        }



        $flight->flightNumber = $request->input('flightNumber');
        $flight->status = $request->input('status');
        $flight->arrivalAirport_id = $codes[$arrivalAirport];
        $flight->arrivalDateTime = $request->input('arrival.datetime');
        $flight->departureAirport_id = $codes[$departureAirport];
        $flight->departureDateTime = $request->input('departure.datetime');

        $flight->save();

        return $this->filterFlights([$flight]);
    }

    protected function filterFlights($flights, $keys = []){
        $data = [];

        foreach ($flights as $flight){
            $entry = [
                'flightNumber' => $flight->flightNumber,
                'status' => $flight->status,
                'href' => route('flights.show', ['id' => $flight->flightNumber])
            ];

            if(in_array('arrivalAirport', $keys)){

                $entry['arrival'] = [
                  'datetime' => $flight->arrivalDateTime,
                  'iataCode' => $flight->arrivalAirport->iataCode,
                  'city' => $flight->arrivalAirport->city,
                  'state' => $flight->arrivalAirport->state,
                ];

            };

            if(in_array('departureAirport', $keys)){

                $entry['departure'] = [
                    'datetime' => $flight->departureDateTime,
                    'iataCode' => $flight->departureAirport->iataCode,
                    'city' => $flight->departureAirport->city,
                    'state' => $flight->departureAirport->state,
                ];

            };

            $data[] = $entry;
        }
        return $data;
    }

    /**
     * @param $parameters
     * @return array
     */
    protected function getWithKeys($parameters)
    {
        $withKeys = [];

        if (isset($parameters['include'])) {

            $includeParams = explode(',', $parameters['include']);

            $includes = array_intersect($this->supportedIncludes, $includeParams);

            $withKeys = array_keys($includes);


        }
        return $withKeys;
    }

    protected function getWhereClause($parameters)
    {
        $clause = [];

        foreach ($this->clauseProperties as $property){

            if(in_array($property, array_keys($parameters))){
                $clause[$property] = $parameters[$property];
            }
        }

        return $clause;
    }

    public function deleteFlight($flightNumber)
    {

        $flight = Flight::where('flightNumber', $flightNumber)->firstOrFail();

        $flight->delete();

    }

    public function validate($flight){
        $validator = Validator::make($flight, $this->roules);

        $validator->validate();
    }
}