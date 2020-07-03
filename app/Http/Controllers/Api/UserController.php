<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Classes\UserWallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Get balance of user
     *
     * @apiParam user_id int
     */
    public function balance(Request $request)
    {
        $this->validate($request, ['user_id' => 'required|exists:users,user_id']);

        $user = User::find($request->get('user_id'), ['wallet']);

        return response(['balance' => $user->wallet]);
    }

    /**
     * Add transaction to user wallet
     *
     * @apiParam user_id int
     * @apiParam amount int this parameter can be negative
     */
    public function addMoney(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|exists:users,user_id',
            'amount' => 'required|numeric'
        ]);

        $userWallet = new UserWallet($request->get('user_id'));
        $userWallet->transaction($request->get('amount'));

        return response(['reference_id' => $userWallet->lastLogId()]);
    }
}
