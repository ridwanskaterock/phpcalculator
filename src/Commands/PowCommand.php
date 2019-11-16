<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Jakmall\Recruitment\Calculator\Storage\StorageService;
use Jakmall\Recruitment\Calculator\Storage\JSON\JSONStorage;
use DateTime;

class PowCommand extends Command
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
    protected static $defaultName = 'pow';
    

    public function __construct()
    {
        parent::__construct();

        $JSONStorage = new JSONStorage;
        $this->storage = new StorageService($JSONStorage);
    }

    public function configure()
    {
        $commandVerb = $this->getCommandVerb();

        $this->signature = sprintf(
            '%s {numbers* : The numbers to be %s}',
            $commandVerb,
            $this->getCommandPassiveVerb()
        );
        $this->description = sprintf('%s all given Numbers', ucfirst($commandVerb));
        $this->addArgument('base', InputArgument::REQUIRED, 'The base number');
        $this->addArgument('exp', InputArgument::REQUIRED, 'The exponent number');
    }   

    protected function getCommandVerb(): string
    {
        return 'pow';
    }

    protected function getCommandPassiveVerb(): string
    {
        return 'exponent';
    }

    public function handle(): void
    {
        $base = $this->getBase();
        $exp = $this->getExp();
        $description = $this->generateCalculationDescription($base, $exp);
        $result = $this->calculate($base, $exp);
        $output = sprintf('%s = %s', $description, $result);

        $this->addHistory($description, $result, $output);

        $this->comment($output);
    }

    protected function getBase()
    {
        return $this->argument('base');
    }

    protected function getExp()
    {
        return $this->argument('exp');
    }

    protected function generateCalculationDescription($base, $exp): string
    {
        $operator = $this->getOperator();
        
        return sprintf('%s %s %s', $base, $operator, $exp);
    }

    protected function getOperator(): string
    {
        return '^';
    }

    /**
     * @param int|float $number1
     * @param int|float $number2
     *
     * @return int|float
     */
    protected function calculate($number1, $number2)
    {
        return pow($number1, $number2);
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