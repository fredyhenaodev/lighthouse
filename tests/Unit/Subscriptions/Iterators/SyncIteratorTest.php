<?php

namespace Tests\Unit\Subscriptions\Iterators;

use Tests\TestCase;
use Illuminate\Support\Collection;
use Nuwave\Lighthouse\Subscriptions\Iterators\SyncIterator;

class SyncIteratorTest extends TestCase
{
    /**
     * @var string
     */
    const EXCEPTION_MESSAGE = 'test_exception';

    /**
     * @var \Nuwave\Lighthouse\Subscriptions\Iterators\SyncIterator
     */
    protected $iterator;

    protected function setUp()
    {
        parent::setUp();

        $this->iterator = new SyncIterator();
    }

    /**
     * @test
     */
    public function itCanIterateOverItemsWithCallback(): void
    {
        $items = [];

        $this->iterator->process(
            $this->items(),
            function ($item) use (&$items): void {
                $items[] = $item;
            }
        );

        $this->assertCount(3, $items);
    }

    /**
     * @test
     */
    public function itCanPassExceptionToHandler(): void
    {
        $exception = null;

        $this->iterator->process(
            $this->items(),
            function ($item) use (&$items): void {
                throw new \Exception(self::EXCEPTION_MESSAGE);
            },
            function ($e) use (&$exception): void {
                $exception = $e;
            }
        );

        $this->assertInstanceOf(\Exception::class, $exception);
        $this->assertSame(self::EXCEPTION_MESSAGE, $exception->getMessage());
    }

    protected function items(): Collection
    {
        return collect([1, 2, 3]);
    }
}
