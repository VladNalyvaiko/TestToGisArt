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
        $maxDriverId = Trip::max('driver_id');

        $data = [
            [
                'driver_id' => $maxDriverId + 1,
                'pickup' => '2024-01-01 13:00:00',
                'dropoff' => '2024-01-01 15:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 1,
                'pickup' => '2024-01-01 10:00:00',
                'dropoff' => '2024-01-01 12:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 2,
                'pickup' => '2024-01-01 10:00:00',
                'dropoff' => '2024-01-01 18:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 2,
                'pickup' => '2024-01-01 13:00:00',
                'dropoff' => '2024-01-01 15:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 2,
                'pickup' => '2024-01-01 17:00:00',
                'dropoff' => '2024-01-01 18:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 2,
                'pickup' => '2024-01-01 22:00:00',
                'dropoff' => '2024-01-01 23:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 10:00:00',
                'dropoff' => '2024-01-01 13:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 17:00:00',
                'dropoff' => '2024-01-01 18:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 9:00:00',
                'dropoff' => '2024-01-01 20:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 2:00:00',
                'dropoff' => '2024-01-01 4:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 9:00:00',
                'dropoff' => '2024-01-01 21:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 9:00:00',
                'dropoff' => '2024-01-01 20:00:00'
            ],
            [
                'driver_id' => $maxDriverId + 3,
                'pickup' => '2024-01-01 6:00:00',
                'dropoff' => '2024-01-01 22:00:00'
            ],
        ];

        Trip::insert($data);

        $response = $this->post('/trips/calculated');

        $response->assertStatus(200)
            ->assertJson([$maxDriverId + 2 => 540])
            ->assertJson([$maxDriverId + 1 => 240])
            ->assertJson([$maxDriverId + 3 => 1080]);
    }
}
