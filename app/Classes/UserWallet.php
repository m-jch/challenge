<?php

namespace App\Classes;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserWallet
{
    /**
     * @int
     */
    private $userId;

    /**
     * @int
     */
    private $lastLogId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function transaction(int $amount): bool
    {
        try {
            DB::beginTransaction();

            $user = User::where('user_id', $this->userId)
                ->lockForUpdate()
                ->first(['user_id', 'wallet']);

            User::where('user_id', $this->userId)
                ->increment('wallet', $amount);

            $this->log($amount, $user->wallet + $amount);

            DB::commit();

            return true;
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return abort(500);
        }
    }

    protected function log(int $amount, int $remainder): void
    {
        $log = new WalletTransaction;
        $log->amount = $amount;
        $log->remainder = $remainder;
        $log->user_id = $this->userId;
        $log->save();

        $this->setLastLogId($log->wallet_transaction_id);
    }

    protected function setLastLogId(int $id): void
    {
        $this->lastLogId = $id;
    }

    public function lastLogId(): int
    {
        return $this->lastLogId;
    }
}