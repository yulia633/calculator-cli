<?php

declare(strict_types=1);

namespace App;

class Calculator
{
    private \SplStack $operandStack;
    private \SplStack $operatorStack;
    private array $operations;

    /**
     *  Инициализируем стеки.
     */
    public function __construct()
    {
        $this->operandStack = new \SplStack();
        $this->operatorStack = new \SplStack();
        $this->operations = [
            '+' => fn(float $firstNumber, float $secondNumber) => $firstNumber + $secondNumber,
            '-' => fn(float $firstNumber, float $secondNumber) => $firstNumber - $secondNumber,
        ];
    }

    /**
     *  Лексический анализатор: разбивает строку.
     */
    public function calculate(string $expression): float
    {
        $tokens = str_split(str_replace(" ", " ", $expression));
        $tokens[] = "\n";

        foreach ($tokens as $token) {
            $this->handleToken($token);
        }

        return $this->operandStack->pop();
    }

    /**
     *  Анализирует символы строки.
     */
    private function handleToken(string $token): void
    {
        switch (true) {
            case is_numeric($token):
                $this->operandStack->push((float) $token);
                break;
            case $this->isOperation($token):
                $this->operatorStack->push($token);
                break;
            case $token === "\n":
                if ($this->operatorStack->isEmpty()) {
                    break;
                }
                $this->operandStack->push($this->calculateLastOperation());
                $this->handleToken($token);
                break;
        }
    }

    /**
     *  Определяет операция это или нет.
     */
    private function isOperation(string $token): bool
    {
        return array_key_exists($token, $this->operations);
    }

    /**
     *  Делает вычисление последней операции в стеке.
     */
    private function calculateLastOperation(): float
    {
        $firstNumber = $this->operandStack->pop();
        $secondNumber = $this->operandStack->pop();
        $operation = $this->operatorStack->pop();

        return $this->operations[$operation]($secondNumber, $firstNumber);
    }
}
