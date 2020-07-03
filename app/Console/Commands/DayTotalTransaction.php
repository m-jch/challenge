<?php

namespace App\Console\Commands;

use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DayTotalTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'day-total-transaction {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get total transaction for given day date';

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
        $day = $this->argument('date');
        if (is_null($day)) {
            $day = Carbon::now();
        } else {
            $day = Carbon::make($day);
        }

        $totalTransaction = WalletTransaction::where('created_at', '>=', $day->startOfDay())
            ->where('created_at', '<=', $day->clone()->endOfDay())
            ->sum('amount');

        $this->info($totalTransaction);
    }
}
