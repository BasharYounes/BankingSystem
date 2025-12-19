<?php

namespace Tests\Unit;

use App\Interfaces\TransactionContract;
use App\Models\Transaction;
use App\Models\TransactionRecord;
use App\Services\StandardTransactionStrategy;
use App\Services\TransactionHandler;
use App\Services\TransactionProcessor;
use Mockery;
use PHPUnit\Framework\TestCase;

class TransactionProcessorTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    // ************  -{ TC1 }-  **************
    public function test_successful_transaction_is_processed(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('execute')
        ->once()
        ->andReturn(true);

        $transaction->shouldReceive('getTransactionId')
        ->andReturn('TXN-TEST-1');

        $transaction->shouldReceive('getAmount')
        ->andReturn(1000);

        $handler = Mockery::mock(TransactionHandler::class);

        $handler->shouldReceive('handle')
        ->once()
        ->with($transaction)
        ->andReturn(true);

        $strategy = Mockery::mock(StandardTransactionStrategy::class);

        $strategy->shouldReceive('process')
        ->once()
        ->with($transaction);

        $processor = new TransactionProcessor();

        $processor->setStrategy($strategy);

        $processor->setHandlerChain($handler);

        $result = $processor->process($transaction);

        $this->assertTrue($result['success']);

    }

    // ************  -{ TC2 }-  **************
    public function test_transaction_fails_when_validation_fails(): void
    {

        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldNotReceive('execute');

        $handler = Mockery::mock(TransactionHandler::class);

        $handler->shouldReceive('handle')
            ->once()
            ->with($transaction)
            ->andReturn(false);

        $strategy = Mockery::mock(StandardTransactionStrategy::class);

        $strategy->shouldNotReceive('process');

        $processor = new TransactionProcessor();

        $processor->setHandlerChain($handler);
        $processor->setStrategy($strategy);

        $result = $processor->process($transaction);

        $this->assertFalse($result['success']);
        $this->assertEquals('validation_failed', $result['reason']);

    }

    // ************  -{ TC3 }-  **************
    public function test_transaction_fails_when_strategy_throws_exception(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldNotReceive('execute');

        $transaction->shouldReceive('getTransactionId')
        ->andReturn('TXN-FAIL-1');


        $handler = Mockery::mock(TransactionHandler::class);

        $handler->shouldReceive('handle')
            ->once()
            ->with($transaction)
            ->andReturn(true);

        $strategy = Mockery::mock(StandardTransactionStrategy::class);

        $strategy->shouldReceive('process')
            ->once()
            ->with($transaction)
            ->andThrow(new \RuntimeException('Fee calculation failed'));

        $processor = new TransactionProcessor();

        $processor->setHandlerChain($handler);
        $processor->setStrategy($strategy);

        $result = $processor->process($transaction);

        $this->assertFalse($result['success']);
        $this->assertEquals('strategy_failed', $result['reason']);
        $this->assertEquals('Fee calculation failed', $result['message']);

    }

    // ************  -{ TC4 }-  **************
    public function test_transaction_fails_when_execute_returns_false(): void
    {
        $transaction = Mockery::mock(TransactionContract::class);

        $transaction->shouldReceive('execute')
            ->once()
            ->andReturn(false);

        $transaction->shouldReceive('getTransactionId')
            ->andReturn('TXN-FAIL-2');

        $handler = Mockery::mock(TransactionHandler::class);
        $handler->shouldReceive('handle')
            ->once()
            ->with($transaction)
            ->andReturn(true);

        $strategy = Mockery::mock(StandardTransactionStrategy::class);
        $strategy->shouldReceive('process')
            ->once()
            ->with($transaction);

        $processor = new TransactionProcessor();
        $processor->setHandlerChain($handler);
        $processor->setStrategy($strategy);

        $result = $processor->process($transaction);

        $this->assertFalse($result['success']);
    }

    // ************  -{ TC5 }-  **************
    public function test_processor_throws_exception_if_strategy_not_set(): void
    {
        $this->expectException(\LogicException::class);

        $transaction = Mockery::mock(TransactionContract::class);

        $handler = Mockery::mock(TransactionHandler::class);
        $handler->shouldReceive('handle')->andReturn(true);

        $processor = new TransactionProcessor();
        $processor->setHandlerChain($handler);

        $processor->process($transaction);
    }

    // ************  -{ TC6 }-  **************
    public function test_processor_throws_exception_if_handler_chain_not_set(): void
    {
        $this->expectException(\LogicException::class);

        $transaction = Mockery::mock(TransactionContract::class);

        $strategy = Mockery::mock(StandardTransactionStrategy::class);

        $processor = new TransactionProcessor();
        $processor->setStrategy($strategy);

        $processor->process($transaction);
    }

}
