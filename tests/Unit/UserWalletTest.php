<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Classes\UserWallet;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserWalletTest extends TestCase
{
    use RefreshDatabase;

    public function testIncreaseWallet()
    {
        $user = factory(\App\Models\User::class)->create();

        $wallet = new UserWallet($user->user_id);
        $result = $wallet->transaction(4000);

        $this->assertTrue($result);

        $this->assertDatabaseHas('users', [
            'wallet' => 4000,
        ]);
    }

    public function testIncreaseLogWallet()
    {
        $user = factory(\App\Models\User::class)->create();

        $wallet = new UserWallet($user->user_id);
        $result = $wallet->transaction(4000);

        $this->assertTrue($result);

        $this->assertDatabaseHas('users', [
            'wallet' => 4000,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->user_id,
            'amount' => 4000,
            'remainder' => 4000
        ]);
    }

    public function testDecreaseWallet()
    {
        $user = factory(\App\Models\User::class)->create();

        $wallet = new UserWallet($user->user_id);
        $result = $wallet->transaction(-4000);

        $this->assertTrue($result);

        $this->assertDatabaseHas('users', [
            'wallet' => -4000,
        ]);
    }

    public function testDecreaseLogWallet()
    {
        $user = factory(\App\Models\User::class)->create();

        $wallet = new UserWallet($user->user_id);
        $result = $wallet->transaction(-4000);

        $this->assertTrue($result);

        $this->assertDatabaseHas('users', [
            'wallet' => -4000,
        ]);

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->user_id,
            'amount' => -4000,
            'remainder' => -4000
        ]);
    }

    public function testRemainderWallet()
    {
        $user = factory(\App\Models\User::class)->create();

        $wallet = new UserWallet($user->user_id);

        $wallet->transaction(-4000);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->user_id,
            'amount' => -4000,
            'remainder' => -4000,
            'wallet_transaction_id' => $wallet->lastLogId()
        ]);

        $wallet->transaction(-500);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->user_id,
            'amount' => -500,
            'remainder' => -4500,
            'wallet_transaction_id' => $wallet->lastLogId()
        ]);

        $wallet->transaction(3560);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->user_id,
            'amount' => 3560,
            'remainder' => -940,
            'wallet_transaction_id' => $wallet->lastLogId()
        ]);

        $wallet->transaction(100);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->user_id,
            'amount' => 100,
            'remainder' => -840,
            'wallet_transaction_id' => $wallet->lastLogId()
        ]);

        $this->assertDatabaseHas('users', [
            'wallet' => -840,
        ]);
    }
}
