<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Jakmall\Recruitment\Calculator\Storage\StorageService;
use Jakmall\Recruitment\Calculator\Storage\JSON\JSONStorage;

class HistoryClearCommand extends Command
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
    protected static $defaultName = 'history:clear';
    
    public function __construct()
    {
        parent::__construct();
        $JSONStorage = new JSONStorage;
        $this->storage = new StorageService($JSONStorage);
    }

    public function configure()
    {
        $commandVerb = $this->getCommandVerb();

        $this->description = sprintf('Clear calculator %s', ucfirst($commandVerb));
    }   

    protected function getCommandVerb(): string
    {
        return 'history';
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearCalculatorHistory($filters);
    }

    public function clearCalculatorHistory(): void
    {
       $this->storage->clear();
       $this->comment('History cleared!');
    }
}