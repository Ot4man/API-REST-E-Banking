<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TransferService
{
    /**
     * Perform a transfer between two accounts.
     */
    public function transfer(array $data, User $user)
    {
        $amount = $data['amount'];
        $fromAccount = Account::findOrFail($data['from_account_id']);
        $toAccount = Account::findOrFail($data['to_account_id']);

        // 🟢 RULE 1: Cannot transfer to the same account
        if ($fromAccount->id === $toAccount->id) {
            throw new Exception("You cannot transfer to the same account.");
        }

        // Ownership/Guardian check
        $this->checkAccessToAccount($fromAccount, $user);

        // Account Status check
        if ($fromAccount->status !== 'ACTIVE') {
            throw new Exception("This account is currently " . $fromAccount->status . ".");
        }

        // Daily limit check (10,000 MAD)
        $this->checkDailyLimit($fromAccount, $amount);

        // Sufficient Funds Check (including overdraft for 'courant')
        $this->checkSufficientFunds($fromAccount, $amount);

        //  Execute within a transaction for safety
        return DB::transaction(function () use ($fromAccount, $toAccount, $amount) {
            // Update FromAccount
            $fromAccount->balance -= $amount;
            $fromAccount->save();

            // Update ToAccount
            $toAccount->balance += $amount;
            $toAccount->save();

            // Record Transactions
            Transaction::create([
                'amount' => -$amount,
                'type' => 'transfer',
                'account_id' => $fromAccount->id
            ]);

            Transaction::create([
                'amount' => $amount,
                'type' => 'transfer',
                'account_id' => $toAccount->id
            ]);

            return [
                'message' => 'Transfer successful',
                'from_account' => $fromAccount,
                'amount' => $amount
            ];
        });
    }

    /**
     * Check if the user has permission to use the account.
     */
    private function checkAccessToAccount(Account $account, User $user)
    {
        // Rule for Minor accounts: Only the guardian can move funds
        if ($account->type === 'mineur') {
            if ($account->guardian_id !== $user->id) {
                throw new Exception("Minor accounts can only be used by their guardian.");
            }
        } else {
            // Standard accounts: User must be an owner
            if (!$account->users()->where('user_id', $user->id)->exists()) {
                throw new Exception("You do not have access to this account.");
            }
        }
    }

    /**
     * Check if the account has enough money (considering overdraft for 'courant').
     */
    private function checkSufficientFunds(Account $account, $amount)
    {
        $limit = ($account->type === 'courant') ? ($account->balance + $account->overdraft_limit) : $account->balance;

        if ($amount > $limit) {
            throw new Exception("Insufficient balance.");
        }
    }

    /**
     * Check the daily limit of 10,000 MAD for transfers.
     */
    private function checkDailyLimit(Account $account, $amount)
    {
        $todayTotal = Transaction::where('account_id', $account->id)
            ->where('type', 'transfer')
            ->where('amount', '<', 0)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');

        // Note: todayTotal is negative because it represents withdrawals
        if ((abs($todayTotal) + $amount) > 10000) {
            throw new Exception("Daily transfer limit of 10,000 MAD reached.");
        }
    }
}
