<?php

namespace Tests\Unit;

use App\Services\ValidationHandler;
use App\Services\TransactionHandler;
use App\Interfaces\TransactionContract;
use App\Models\Transaction;
use Mockery;
use Tests\TestCase;

class ValidationHandlerTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // ===================================================
    // TC1: Validation passes and next handler is called
    // ===================================================
    public function test_validation_passes_and_calls_next_handler(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(100);

        $transaction->shouldReceive('getStatus')
            ->once()
            ->andReturn(Transaction::STATUS_PENDING);

        $nextHandler = Mockery::mock(TransactionHandler::class);
        $nextHandler->shouldReceive('handle')
            ->once()
            ->with($transaction)
            ->andReturn(true);

        $handler = new ValidationHandler();
        $handler->setNext($nextHandler);

        $result = $handler->handle($transaction);

        $this->assertTrue($result);
    }

    // ===================================================
    // TC2: Validation fails due to invalid amount
    // ===================================================
    public function test_validation_fails_when_amount_is_invalid(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(0); // invalid

        // getStatus SHOULD NOT be called
        $transaction->shouldNotReceive('getStatus');

        $handler = new ValidationHandler();

        $result = $handler->handle($transaction);

        $this->assertFalse($result);
    }

    // ===================================================
    // TC3: Validation fails due to invalid status
    // ===================================================
    public function test_validation_fails_when_status_is_not_pending(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(100);

        $transaction->shouldReceive('getStatus')
            ->once()
            ->andReturn(Transaction::STATUS_COMPLETED); // invalid

        $handler = new ValidationHandler();

        $result = $handler->handle($transaction);

        $this->assertFalse($result);
    }

    // ===================================================
    // TC4: Validation passes with NO next handler
    // ===================================================
    public function test_validation_passes_without_next_handler(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('getAmount')
            ->once()
            ->andReturn(100);

        $transaction->shouldReceive('getStatus')
            ->once()
            ->andReturn(Transaction::STATUS_PENDING);

        $handler = new ValidationHandler();

        $result = $handler->handle($transaction);

        $this->assertTrue($result);
    }
}
