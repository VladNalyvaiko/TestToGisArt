<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Trip;
use Database\Seeders\TripSeeder;

class TripControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This method is called before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(TripSeeder::class);
    }

    /**
     * Test for index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Test for getAll method
     *
     * @return void
     */
    public function testGetAll(): void
    {
        $trip = Trip::first();

        $response = $this->post('/trips');

        $response->assertStatus(200)
            ->assertSee($trip->driver_id);
    }

    /**
     * Test for getCalculatePayableTimeForAll method
     *
     * @return void
     */
    public function testGetCalculatePayableTimeForAll(): void
    {
        $driver_id = Trip::first()->driver_id;
        $trips = Trip::where('driver_id', $driver_id)->get();
        $dates = [];
        foreach ($trips as $item) {
            $startTime = strtotime($item->pickup);
            $endTime = strtotime($item->dropoff);

            $updated = false;
            if (!empty($dates)) {
                foreach ($dates as &$interval) {
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
                $dates[] = [
                    $item["pickup"],
                    $item["dropoff"]
                ];
            }
        }

        $totalTime = 0;
        foreach ($dates as $interval) {
            $pickupTime = strtotime($interval[0]);
            $dropoffTime = strtotime($interval[1]);
            $totalTime += ($dropoffTime - $pickupTime) / 60;
        }

        $response = $this->post('/trips/calculated');

        $response->assertStatus(200)
            ->assertJson([$driver_id => round($totalTime)]);
    }
}
