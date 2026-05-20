<?php

namespace App\Console\Commands;

use App\Models\CheckUpFollowUp;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendFollowUp extends Command
{
    protected $signature   = 'followup:send';
    protected $description = 'Kirim WhatsApp pengabaran via Fonnte untuk follow-up yang jatuh tempo hari ini';

    public function handle(): int
    {
        $token = config('services.fonnte.token');

        if (empty($token)) {
            $this->error('FONNTE_TOKEN belum diset di .env');
            return self::FAILURE;
        }

        $today = Carbon::today()->toDateString();

        $followUps = CheckUpFollowUp::where('status', 'pending')
            ->where('scheduled_date', '<=', $today)
            ->get();

        if ($followUps->isEmpty()) {
            $this->info('Tidak ada follow-up yang perlu dikirim hari ini.');
            return self::SUCCESS;
        }

        $this->info("Mengirim {$followUps->count()} follow-up...");

        foreach ($followUps as $followUp) {
            $phone = $this->normalizePhone($followUp->owner_phone);

            try {
                $response = Http::timeout(15)
                    ->withHeaders(['Authorization' => $token])
                    ->post('https://api.fonnte.com/send', [
                        'target'  => $phone,
                        'message' => $followUp->message,
                    ]);

                $body = $response->json();

                if ($response->successful() && ($body['status'] ?? false) === true) {
                    $followUp->update([
                        'status'  => 'sent',
                        'sent_at' => Carbon::now(),
                    ]);
                    $this->line("  ✓ Terkirim ke {$phone} ({$followUp->owner_name} — {$followUp->pet_name})");
                } else {
                    $errMsg = $body['reason'] ?? $response->body();
                    $followUp->update([
                        'status'        => 'failed',
                        'error_message' => $errMsg,
                    ]);
                    $this->warn("  ✗ Gagal ke {$phone}: {$errMsg}");
                    Log::warning("SendFollowUp gagal #{$followUp->id}: {$errMsg}");
                }
            } catch (\Throwable $e) {
                $followUp->update([
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                $this->error("  ✗ Error #{$followUp->id}: {$e->getMessage()}");
                Log::error("SendFollowUp exception #{$followUp->id}: {$e->getMessage()}");
            }
        }

        $this->info('Selesai.');
        return self::SUCCESS;
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }
}
