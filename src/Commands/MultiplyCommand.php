<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Jakmall\Recruitment\Calculator\Storage\StorageService;
use Jakmall\Recruitment\Calculator\Storage\JSON\JSONStorage;
use DateTime;

class MultiplyCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected static $defaultName = 'multiply';

    public function __construct()
    {
        parent::__construct();

        $JSONStorage = new JSONStorage;
        $this->storage = new StorageService($JSONStorage);
    }

    public function configure()
    {
        $commandVerb = $this->getCommandVerb();
        $commandPassiveVerb = $this->getCommandPassiveVerb();

        $this->signature = sprintf(
            '%s {numbers* : The numbers to be %s}',
            $commandVerb,
            $this->getCommandPassiveVerb()
        );
        $this->description = sprintf('%s all given Numbers', ucfirst($commandVerb));
        $this->addArgument('numbers', InputArgument::IS_ARRAY, sprintf('The number to be %s', ucfirst($commandPassiveVerb)));
    }   

    protected function getCommandVerb(): string
    {
        return 'multiply';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'multiplied';
    }

    public function handle(): void
    {
        $numbers = $this->getInput();
        $description = $this->generateCalculationDescription($numbers);
        $result = $this->calculateAll($numbers);
        $output = sprintf('%s = %s', $description, $result);

        $this->addHistory($description, $result, $output);

        $this->comment($output);

    }

    protected function getInput(): array
    {
        return $this->argument('numbers');
    }

    protected function generateCalculationDescription(array $numbers): string
    {
        $operator = $this->getOperator();
        $glue = sprintf(' %s ', $operator);

        return implode($glue, $numbers);
    }

    protected function getOperator(): string
    {
        return '*';
    }

    /**
     * @param array $numbers
     *
     * @return float|int
     */
    protected function calculateAll(array $numbers)
    {
        $number = array_pop($numbers);

        if (count($numbers) <= 0) {
            return $number;
        }

        return $this->calculate($this->calculateAll($numbers), $number);
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return $number1 * $number2;
    }

    /**
     * @param string $description
     * @param double $result
     * @param string $output
     *
     * @return int|float
     */
    protected function addHistory($description, $result, $output): void
    {
        $this->storage
            ->insert([
                'command' => $this->getCommandVerb(),
                'description' => $description, 
                'result' => $result, 
                'output' => $output, 
                'time' => (new DateTime)->format('Y-m-d H:i:s')
            ])
        ;
    }
}