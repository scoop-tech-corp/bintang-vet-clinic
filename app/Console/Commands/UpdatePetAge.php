<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdatePetAge extends Command
{
    protected $signature   = 'pet:update-age';
    protected $description = 'Perbarui tahun, bulan, dan hari usia hewan berdasarkan pet_birth_date';

    public function handle(): int
    {
        // Satu query UPDATE — efisien untuk ribuan data sekaligus.
        // TIMESTAMPDIFF(YEAR,  birth, today)       → selisih tahun penuh
        // TIMESTAMPDIFF(MONTH, birth, today) % 12  → sisa bulan setelah tahun dikurangi
        // DATEDIFF(today, birth + tahun + bulan)   → sisa hari setelah tahun+bulan dikurangi
        $affected = DB::statement("
            UPDATE patients
            SET
                pet_year_age  = TIMESTAMPDIFF(YEAR, pet_birth_date, CURDATE()),

                pet_month_age = TIMESTAMPDIFF(MONTH, pet_birth_date, CURDATE()) % 12,

                pet_day_age   = DATEDIFF(
                                    CURDATE(),
                                    DATE_ADD(
                                        DATE_ADD(
                                            pet_birth_date,
                                            INTERVAL TIMESTAMPDIFF(YEAR, pet_birth_date, CURDATE()) YEAR
                                        ),
                                        INTERVAL (TIMESTAMPDIFF(MONTH, pet_birth_date, CURDATE()) % 12) MONTH
                                    )
                                )
            WHERE pet_birth_date IS NOT NULL
              AND pet_birth_date <= CURDATE()
              AND isDeleted = 0
        ");

        $count = DB::select('SELECT ROW_COUNT() as n')[0]->n ?? 0;

        $this->info("Usia hewan diperbarui: {$count} pasien.");
        Log::info("UpdatePetAge: {$count} pasien diperbarui pada " . now()->toDateTimeString());

        return self::SUCCESS;
    }
}
