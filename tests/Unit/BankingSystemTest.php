<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;
use App\Services\AccountService;
use App\Services\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Exception;

class BankingSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Successful Current Account Creation (AccountService)
     */
    public function test_it_creates_a_courant_account_successfully()
    {
        $user = User::create([
            'name' => 'omar',
            'email' => 'omar@test.com',
            'password' => Hash::make('password'),
            'date_naissance' => '1990-01-01',
            'role' => 'user'
        ]);

        $service = new AccountService();
        $account = $service->createAccount([
            'type' => 'courant',
            'overdraft_limit' => 500
        ], $user);

        $this->assertEquals('courant', $account->type);
        $this->assertEquals(500, $account->overdraft_limit);
        $this->assertTrue($account->users->contains($user->id));
    }

    /**
     * Test 2: Prevent creating Minor Account for an adult (AccountService)
     */
    public function test_it_fails_to_create_minor_account_for_non_minor_user()
    {
        $adultUser = User::create([
            'name' => 'ali',
            'email' => 'ali@test.com',
            'password' => 'password',
            'date_naissance' => '1990-01-01',
            'role' => 'user'
        ]);

        $service = new AccountService();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('User is not a minor. Choose a different account type.');

        $service->createAccount([
            'type' => 'mineur',
            'guardian_id' => 1
        ], $adultUser);
    }

    /**
     * Test 3: Successful Transfer between two accounts (TransferService)
     */
    public function test_it_successfully_transfers_money()
    {
        $user = User::create([
            'name' => 'yassine',
            'email' => 'yassine@test.com',
            'password' => 'password',
            'date_naissance' => '1990-01-01',
            'role' => 'user'
        ]);

        $acc1 = Account::create(['type' => 'courant', 'balance' => 1000, 'status' => 'ACTIVE']);
        $acc2 = Account::create(['type' => 'courant', 'balance' => 500, 'status' => 'ACTIVE']);
        $acc1->users()->attach($user->id);

        $service = new TransferService();
        $service->transfer([
            'from_account_id' => $acc1->id,
            'to_account_id' => $acc2->id,
            'amount' => 300
        ], $user);

        $this->assertEquals(700, $acc1->fresh()->balance);
        $this->assertEquals(800, $acc2->fresh()->balance);
    }

    /**
     * Test 4: Fail transfer when balance is insufficient (TransferService)
     */
    public function test_it_fails_transfer_due_to_insufficient_funds()
    {
        $user = User::create([
            'name' => 'younes',
            'email' => 'younes@test.com',
            'password' => 'password',
            'date_naissance' => '1990-01-01',
            'role' => 'user'
        ]);

        $acc1 = Account::create(['type' => 'epargne', 'balance' => 100, 'status' => 'ACTIVE']);
        $acc2 = Account::create(['type' => 'epargne', 'balance' => 100, 'status' => 'ACTIVE']);
        $acc1->users()->attach($user->id);

        $service = new TransferService();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Insufficient balance.');

        $service->transfer([
            'from_account_id' => $acc1->id,
            'to_account_id' => $acc2->id,
            'amount' => 500
        ], $user);
    }

    /**
     * Test 5: Prevent transfers from Blocked accounts (TransferService)
     */
    public function test_it_prevents_transfers_from_blocked_accounts()
    {
        $user = User::create([
            'name' => 'asma',
            'email' => 'asma@test.com',
            'password' => 'password',
            'date_naissance' => '1990-01-01',
            'role' => 'user'
        ]);

        $acc1 = Account::create(['type' => 'courant', 'balance' => 1000, 'status' => 'BLOCKED']);
        $acc2 = Account::create(['type' => 'courant', 'balance' => 500, 'status' => 'ACTIVE']);
        $acc1->users()->attach($user->id);

        $service = new TransferService();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('This account is currently BLOCKED.');

        $service->transfer([
            'from_account_id' => $acc1->id,
            'to_account_id' => $acc2->id,
            'amount' => 100
        ], $user);
    }
}
