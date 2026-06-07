<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillPetAgeRegistrationsFixDeleted extends Migration
{
    public function up()
    {
        // Mengisi sisa registrations yang masih NULL karena sebelumnya ter-skip:
        // 1. Registrations dengan isDeleted = 1 (soft-deleted) → tetap perlu diisi untuk histori
        // 2. Registrations yang patient-nya juga soft-deleted → tetap ambil data patient-nya
        DB::statement("
            UPDATE registrations r
            JOIN (
                SELECT
                    p.id,
                    COALESCE(
                        p.pet_birth_date,
                        DATE_SUB(DATE(p.created_at), INTERVAL (
                            COALESCE(p.pet_year_age, 0) * 365 +
                            COALESCE(p.pet_month_age, 0) * 30 +
                            COALESCE(p.pet_day_age, 0)
                        ) DAY)
                    ) AS effective_birth_date
                FROM patients p
            ) p_eff ON r.patient_id = p_eff.id
            SET
                r.pet_year_age = TIMESTAMPDIFF(YEAR, p_eff.effective_birth_date, DATE(r.created_at)),

                r.pet_month_age = TIMESTAMPDIFF(MONTH, p_eff.effective_birth_date, DATE(r.created_at))
                                - (TIMESTAMPDIFF(YEAR, p_eff.effective_birth_date, DATE(r.created_at)) * 12),

                r.pet_day_age = DATEDIFF(
                                  DATE(r.created_at),
                                  DATE_ADD(p_eff.effective_birth_date,
                                    INTERVAL TIMESTAMPDIFF(MONTH, p_eff.effective_birth_date, DATE(r.created_at)) MONTH
                                  )
                                )
            WHERE r.pet_year_age IS NULL
               OR r.pet_month_age IS NULL
               OR r.pet_day_age IS NULL
        ");
    }

    public function down()
    {
        DB::statement("
            UPDATE registrations
            SET pet_year_age  = NULL,
                pet_month_age = NULL,
                pet_day_age   = NULL
        ");
    }
}
