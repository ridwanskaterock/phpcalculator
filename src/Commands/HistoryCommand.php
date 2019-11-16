<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Jakmall\Recruitment\Calculator\Storage\StorageService;
use Jakmall\Recruitment\Calculator\Storage\JSON\JSONStorage;

class HistoryCommand extends Command
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
    protected static $defaultName = 'history:list';
    
    public function __construct()
    {
        parent::__construct();
        $JSONStorage = new JSONStorage;
        $this->storage = new StorageService($JSONStorage);
    }

    public function configure()
    {
        $commandVerb = $this->getCommandVerb();

        $this->description = sprintf('Show calculator %s', ucfirst($commandVerb));
        $this->addArgument('filters', InputArgument::IS_ARRAY, sprintf('Filter the %s by commands', $commandVerb));
    }   

    protected function getCommandVerb(): string
    {
        return 'history';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $filters = $this->getInput();

        $this->filterValidated($filters);
        $this->executeFilter($output, $filters);
    }

    public function filterValidated(Array $filters): void
    {
        $allowedFilters = $this->getAllowedCalculatorFilter();
        foreach ($filters as $filter) {
            if (!in_array($filter, $allowedFilters)) {
                $this->error(
                    sprintf('Filter "%s" is not avaiable, avaiable only %s', 
                        $filter, 
                        implode($allowedFilters, ', ')
                    )
                ); 
                $this->close();
            }
        }   
    }

    public function executeFilter($output, $filters): void
    {
        $histories = $this->storage->get($filters);

        if (count($histories) == 0) {
            $this->comment('History is empty');
            $this->close();
        }

        $table = new Table($output);
        $table
            ->setHeaders(['No', 'Command', 'Description', 'Result', 'Output', 'Time'])
            ->setRows($histories)
        ;
        $table->render();
    }

    public function getAllowedCalculatorFilter(): array
    {
        return [
            'add', 
            'substract',
            'multiply',
            'divide',
            'pow',
        ];
    }

    protected function getInput(): array
    {
        return $this->argument('filters');
    }

    public function close(): void 
    {
        die;
    }
}