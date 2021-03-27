<?php

declare(strict_types=1);

namespace App;

class Calculator
{
    private \SplStack $operandStack;
    private \SplStack $operatorStack;
    private $operations;

    /**
     * @var array $validationData
     */
    protected $validationData = [];


    /**
     *  Инициализируем стеки.
     */
    public function __construct()
    {
        $this->operandStack = new \SplStack();
        $this->operatorStack = new \SplStack();
        $this->operations = [
            '+' => [
                'operation' => fn(float $firstNumber, float $secondNumber) => $firstNumber + $secondNumber,
                'priority' => 1,
            ],
            '-' => [
                'operation' => fn(float $firstNumber, float $secondNumber) => $firstNumber - $secondNumber,
                'priority' => 1,
            ],
            '*' => [
                'operation' => fn(float $firstNumber, float $secondNumber) => $firstNumber * $secondNumber,
                'priority' => 2,
            ],
            '/' => [
                'operation' => fn(float $firstNumber, float $secondNumber) => $firstNumber / $secondNumber,
                'priority' => 2,
            ],
        ];
    }

    /**
     * Лексический анализатор: разбивает строку.
     * @param string $expression
     * @return float
     */
    public function calculate(string $expression): float
    {
        $tokens = str_split(str_replace(" ", " ", $expression));
        $tokens[] = "\n";

        foreach ($tokens as $token) {
            if ($this->validate($token) && $token !== "\n") {
                $this->print('Неверное значение символа, введите цифры или одну из следующих операций: + , -, *, /');
            }

            $this->handleToken($token);
        }

        return $this->operandStack->pop();
    }

    /**
     * Анализирует символы строки.
     * @param string $token
     * @return void
     */
    private function handleToken(string $token): void
    {
        switch (true) {
            case is_numeric($token):
                $this->operandStack->push((float) $token);
                break;
            case $this->isOperation($token):
                if ($this->operatorStack->isEmpty()) {
                    $this->operatorStack->push($token);
                    break;
                }
                //Операция, которая готовится попасть в стек
                $currentOperation = $this->operations[$token];
                //Предыдущий оператор, которая лежит в стеке
                $previousOperator = $this->operatorStack->top();

                //Проверить лежит ли скобка в стеке
                if (!$this->isOperation($previousOperator)) {
                    $this->operatorStack->push($token);
                    break;
                }
                //Проверить приоритет
                $previousOperation = $this->operations[$previousOperator];

                if ($previousOperation['priority'] > $currentOperation['priority']) {
                    $this->operandStack->push($this->calculateLastOperation());
                    $this->handleToken($token);
                } else {
                    $this->operatorStack->push($token);
                }
                break;
            case $token === "(":
                $this->operatorStack->push($token);
                break;
            case $token === ")":
                if ($this->operatorStack->top() === "(") {
                    $this->operatorStack->pop();
                    break;
                }
                $this->operandStack->push($this->calculateLastOperation());
                $this->handleToken($token);
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
     * Определяет операция это или нет.
     * @param string $token
     * @return bool
     */
    private function isOperation(string $token): bool
    {
        return array_key_exists($token, $this->operations);
    }

    /**
     * Делает вычисление последней операции в стеке.
     * @return float
     */
    private function calculateLastOperation(): float
    {
        $firstNumber = $this->operandStack->pop();
        $secondNumber = $this->operandStack->pop();
        $operation = $this->operatorStack->pop();

        return $this->operations[$operation]['operation']($secondNumber, $firstNumber);
    }

    /**
     * Проверяет регуляркой на соответсвие
     * ожидаемым символам + , -, *, /, (, )
     * @param string $string
     * @return bool
     */
    protected function validate(string $string): bool
    {
        preg_match('/^(([0-9]|\-|\+|\*|\/|\(|\)|) *)*/', $string, $this->validationData);

        return empty($this->validationData[0]);
    }

     /**
     * Печатает сообщение
     * @param string $msg
     */
    protected function print(string $msg): void
    {
        echo $msg . PHP_EOL;
    }
}
