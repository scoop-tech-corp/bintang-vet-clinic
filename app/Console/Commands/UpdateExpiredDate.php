<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class UpdateExpiredDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message:updateExpiredDate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Date Expired Every Midnight day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('list_of_items')
            ->where('expired_date', '!=', '0000-00-00')
            ->update(['diff_expired_days' => DB::raw('DATEDIFF(list_of_items.expired_date,NOW())')]);

        return 1;
    }
}