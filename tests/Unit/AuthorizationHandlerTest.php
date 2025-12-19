<?php

namespace Tests\Unit;

use App\Services\AuthorizationHandler;
use App\Services\TransactionHandler;
use App\Interfaces\TransactionContract;
use Mockery;
use Tests\TestCase;

class AuthorizationHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ===================================================
    // TC1: Transaction requires approval (amount > limit)
    // ===================================================
    public function test_transaction_requires_approval_when_amount_exceeds_limit(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(6000); // أكبر من 5000

        $transaction->shouldReceive('setStatus')
            ->once()
            ->with('requires_approval');

        $handler = new AuthorizationHandler();

        $result = $handler->handle($transaction);

        $this->assertFalse($result);
    }

    // ===================================================
    // TC2: Transaction does NOT require approval and passes to next handler
    // ===================================================
    public function test_transaction_passes_to_next_handler_when_no_approval_needed(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(1000); // أقل من limit

        $nextHandler = Mockery::mock(TransactionHandler::class);
        $nextHandler->shouldReceive('handle')
            ->once()
            ->with($transaction)
            ->andReturn(true);

        $handler = new AuthorizationHandler();
        $handler->setNext($nextHandler);

        $result = $handler->handle($transaction);

        $this->assertTrue($result);
    }

    // ===================================================
    // TC3: Transaction does NOT require approval and no next handler exists
    // ===================================================
    public function test_transaction_passes_when_no_approval_needed_and_no_next_handler(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(2000);

        $handler = new AuthorizationHandler();

        $result = $handler->handle($transaction);

        $this->assertTrue($result);
    }

    // ===================================================
    // TC4: Status is set to requires_approval when approval is needed
    // ===================================================
    public function test_status_is_updated_when_transaction_requires_approval(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(8000);

        $transaction->shouldReceive('setStatus')
            ->once()
            ->with('requires_approval');

        $handler = new AuthorizationHandler();

        $handler->handle($transaction);

        $this->assertTrue(true); // التأكيد تم عبر mock expectations
    }
}
