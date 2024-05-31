<?php

namespace App\Http\Controllers;

use App\Models\Trip;

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
     * Calculate time for drivers
     *
     * @param iterable $data // collection Trip Models
     *
     * @return array
     */
    public function calculatePayableTime(iterable $data)
    {
        $result = [];

        foreach ($data as $item) {
            $startTime = strtotime($item->pickup);
            $endTime = strtotime($item->dropoff);

            // Update an existing interval or add a new one
            $updated = false;
            if (array_key_exists($item->driver_id, $result)) {
                foreach ($result[$item->driver_id] as &$interval) {
                    $intervalStart = strtotime($interval[0]);
                    $intervalEnd = strtotime($interval[1]);
                    if (($startTime <= $intervalEnd && $startTime >= $intervalStart)
                        || ($endTime >= $intervalStart && $endTime <= $intervalEnd)
                    ) {
                        $interval[0] = min($interval[0], $item["pickup"]);
                        $interval[1] = max($interval[1], $item["dropoff"]);
                        $updated = true;
                        break;
                    }
                }
            }

            if (!$updated) {
                $result[$item->driver_id][] = [
                    $item["pickup"],
                    $item["dropoff"]
                ];
            }
        }

        // We calculate the total paid time for each driver
        foreach ($result as $driverId => &$intervals) {
            $totalTime = 0;
            foreach ($intervals as $interval) {
                $pickupTime = strtotime($interval[0]);
                $dropoffTime = strtotime($interval[1]);
                $totalTime += ($dropoffTime - $pickupTime) / 60; // Convert to minutes
            }
            $result[$driverId] = round($totalTime);
        }

        return $result;
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
        return $this->calculatePayableTime($data);
    }
}
