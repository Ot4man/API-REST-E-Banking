<?php

namespace App\Services;

use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Exception;

class AccountService
{
    /**
     * Create a new account based on type and user constraints.
     */
    public function createAccount(array $data, User $user)
    {
        $type = $data['type'];

        if (!in_array($type, ['courant', 'epargne', 'mineur'])) {
            throw new Exception('Invalid account type');
        }

        //MINEUR
        if ($type === 'mineur') {
            $age = Carbon::parse($user->date_naissance)->age;

            if ($age >= 18) {
                throw new Exception('User is not a minor. Choose a different account type.');
            }

            if (empty($data['guardian_id'])) {
                throw new Exception('A guardian is required for minor accounts.');
            }

            // Optional: check if guardian exists
            if (!User::find($data['guardian_id'])) {
                throw new Exception('The specified guardian does not exist.');
            }

            $account = Account::create([
                'type' => $type,
                'guardian_id' => $data['guardian_id'],
                'interest_rate' => $data['interest_rate'] ?? 2.5,
                'status' => 'ACTIVE'
            ]);

            // Both user and guardian are linked to the account
            $account->users()->attach($user->id);
            $account->users()->attach($data['guardian_id']);

            return $account->load(['users', 'guardian']);
        }

        //  COURANT ou EPARGNE
        $account = Account::create([
            'type' => $type,
            'overdraft_limit' => $type === 'courant' ? ($data['overdraft_limit'] ?? 0) : null,
            'interest_rate' => $type === 'epargne' ? ($data['interest_rate'] ?? 3.0) : null,
            'status' => 'ACTIVE'
        ]);

        $account->users()->attach($user->id);

        return $account->load('users');
    }
}
