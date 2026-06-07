<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class BackfillPetAgeInRegistrations extends Migration
{
    public function up()
    {
        // Isi pet_year_age, pet_month_age, pet_day_age di registrations
        // berdasarkan usia hewan PADA SAAT pendaftaran (bukan usia saat ini).
        //
        // Jika pet_birth_date tersedia di patients → hitung langsung.
        // Jika NULL → estimasi birth date dari usia tersimpan + created_at pasien.
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
                WHERE p.isDeleted = 0
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
            WHERE r.isDeleted = 0
              AND (r.pet_year_age IS NULL OR r.pet_month_age IS NULL OR r.pet_day_age IS NULL)
        ");
    }

    public function down()
    {
        // Kembalikan ke NULL hanya untuk baris yang sebelumnya kosong.
        // Karena tidak bisa membedakan mana yang diisi migration ini vs manual,
        // down() ini mengosongkan semua — jalankan hanya jika perlu rollback penuh.
        DB::statement("
            UPDATE registrations r
            JOIN patients p ON r.patient_id = p.id
            SET
                r.pet_year_age  = NULL,
                r.pet_month_age = NULL,
                r.pet_day_age   = NULL
            WHERE r.isDeleted = 0
        ");
    }
}
