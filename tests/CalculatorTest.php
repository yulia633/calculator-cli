<?php

declare(strict_types=1);

namespace Tests;

use App\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     * @param string $expression
     * @param float $expexted
     */
    public function testCalculate(string $expression, float $expexted): void
    {
        $calculator = new Calculator();

        $this->assertEquals($expexted, $calculator->calculate($expression));
    }

    public function dataProvider(): array
    {
        return [
            '1 + 1' => [
                '1 + 1',
                2,
            ],
            '1 + 4 - 2' => [
                '1 + 4 - 2',
                3,
            ],
            '1 + 2 * 2' => [
                '1 + 2 * 2',
                5,
            ],
            '3 - 4 / 2' => [
                '3 - 4 / 2',
                1,
            ],
            '3 - (4 + 2) * 4' => [
                '3 - (4 + 2) * 4',
                -21,
            ],
        ];
    }
}
