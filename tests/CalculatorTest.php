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
                2
            ],
        ];
    }
}
