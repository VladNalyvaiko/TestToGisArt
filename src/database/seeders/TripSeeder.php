<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Trip;


class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = fopen(base_path("database/data/trips.csv"), "r");

        $firstline = true;
        $result = [];
        while (($data = fgetcsv($csvFile, 2000, ",")) !== false) {
            if (!$firstline) {
                $result[] = [
                    'id' => $data['0'],
                    'driver_id' => $data['1'],
                    'pickup' => $data['2'],
                    'dropoff' => $data['3'],
                ];
            }
            $firstline = false;
        }
        fclose($csvFile);

        Trip::insert($result);
    }
}
