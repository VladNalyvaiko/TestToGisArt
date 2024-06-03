<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Lib\DriverPayTimeCalculator;

class TripController extends Controller
{
    /**
     * Index page
     *
     * @return View
     */
    public function index()
    {
        return view('index');
    }

    /**
     * Get all trips
     *
     * @return iterable // collection Trips Models
     */
    public function getAll()
    {
        return Trip::get();
    }

    /**
     * Get calculate payable time for all
     *
     * @return array // array [driver_id => minutes]
     */
    public function getCalculatePayableTimeForAll()
    {
        $data = Trip::get();
        return DriverPayTimeCalculator::calculatePayableTimeForDrivers($data);
    }
}
