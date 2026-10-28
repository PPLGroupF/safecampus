<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan seeder sesuai dependensi data.
     * Seeder yang belum dibuat (PBI-nya belum masuk) otomatis dilewati.
     */
    private const SEEDERS = [
        'CampusSeeder',
        'UnitSeeder',
        'CategorySeeder',
        'UserSeeder',
        'ReportSeeder',
    ];

    public function run(): void
    {
        foreach (self::SEEDERS as $name) {
            $class = __NAMESPACE__.'\\'.$name;

            if (class_exists($class)) {
                $this->call($class);
            }
        }
    }
}
