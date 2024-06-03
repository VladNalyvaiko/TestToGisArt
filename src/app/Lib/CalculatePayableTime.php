<?php

namespace App\Lib;

/**
 * Class DriverPayTimeCalculator.
 */
class DriverPayTimeCalculator
{
    /**
     * Calculate time for drivers
     *
     * @param iterable $data // collection Trip Models
     *
     * @return array
     */
    public static function calculatePayableTimeForDrivers(iterable $data)
    {
        $result = [];

        foreach ($data as $item) {
            $startTime = strtotime($item->pickup);
            $endTime = strtotime($item->dropoff);

            if (array_key_exists($item->driver_id, $result)) {
                foreach ($result[$item->driver_id] as $key => &$interval) {
                    $intervalStart = strtotime($interval["pickup"]);
                    $intervalEnd = strtotime($interval["dropoff"]);

                    if (($startTime <= $intervalEnd && $startTime >= $intervalStart)
                        && ($endTime >= $intervalStart && $endTime <= $intervalEnd)) {
                        break;
                    }
                    if (($startTime <= $intervalEnd && $startTime >= $intervalStart)
                        || ($endTime >= $intervalStart && $endTime <= $intervalEnd)
                    ) {
                        $interval['pickup'] = min($interval["pickup"], $item["pickup"]);
                        $interval['dropoff'] = max($interval["dropoff"], $item["dropoff"]);
                        break;
                    }

                    if ($key === array_key_last($result[$item->driver_id])) {
                        $result[$item->driver_id][] = [
                            'pickup' => $item->pickup,
                            'dropoff' => $item->dropoff
                        ];
                    }
                }
            } else {
                $result[$item->driver_id][] = [
                    'pickup' => $item->pickup,
                    'dropoff' => $item->dropoff
                ];
            }
        }

        // We calculate the total paid time for each driver
        foreach ($result as $driverId => $trips) {
            $totalTime = 0;
            foreach ($trips as $times) {
                $pickupTime = strtotime($times["pickup"]);
                $dropoffTime = strtotime($times["dropoff"]);
                $totalTime += ($dropoffTime - $pickupTime);
            }
            $result[$driverId] = round($totalTime / 60);
        }

        return $result;
    }
}
