<?php

namespace Tests\Unit\Schema\Types\Scalars;

use Carbon\Carbon;
use Tests\TestCase;
use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\IntValueNode;
use GraphQL\Language\AST\StringValueNode;
use Nuwave\Lighthouse\Schema\Types\Scalars\Date;

class DateTest extends TestCase
{
    /**
     * @test
     * @dataProvider invalidDateValues
     *
     * @param  mixed  $value
     */
    public function itThrowsIfSerializingNonString($value): void
    {
        $this->expectException(InvariantViolation::class);

        (new Date())->serialize($value);
    }

    /**
     * @test
     * @dataProvider invalidDateValues
     *
     * @param  mixed  $value
     */
    public function itThrowsIfParseValueNonString($value): void
    {
        $this->expectException(Error::class);

        (new Date())->parseValue($value);
    }

    /**
     * Those values should fail passing as a date.
     *
     * @return mixed[]
     */
    public function invalidDateValues(): array
    {
        return [
            [1],
            ['rolf'],
            [new class() {
            }],
            [null],
            [''],
        ];
    }

    /**
     * @test
     */
    public function itParsesValueString(): void
    {
        $date = '2018-10-01';
        $this->assertEquals(
            (new Carbon($date))->startOfDay(),
            (new Date())->parseValue($date)
        );
    }

    /**
     * @test
     */
    public function itParsesLiteral(): void
    {
        $dateLiteral = new StringValueNode(
            ['value' => '2018-10-01']
        );
        $result = (new Date())->parseLiteral($dateLiteral);

        $this->assertSame(
            $dateLiteral->value,
            $result->toDateString()
        );
    }

    /**
     * @test
     */
    public function itThrowsIfParseLiteralNonString(): void
    {
        $this->expectException(Error::class);

        (new Date())->parseLiteral(
            new IntValueNode([])
        );
    }

    /**
     * @test
     */
    public function itSerializesCarbonInstance(): void
    {
        $now = now();
        $result = (new Date())->serialize($now);

        $this->assertSame(
            $now->toDateString(),
            $result
        );
    }

    /**
     * @test
     */
    public function itSerializesValidDateString(): void
    {
        $date = '2018-10-01';
        $result = (new Date())->serialize($date);

        $this->assertSame(
            $date,
            $result
        );
    }
}
