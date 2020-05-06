<?php


namespace PersonalizedSearchBundle\src\Command;

use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PurchaseHistoryInterface;
use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ETLCommand extends AbstractCommand
{
    private $purchaseHistory;
    public function __construct(PurchaseHistoryInterface $purchaseHistory)
    {
        $this->purchaseHistory = $purchaseHistory;
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('personalizedsearch:start-etl')
            ->setDescription('Manually triggers the ETL Mechanism');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->purchaseHistory->updateOrderIndexFromOrderDb();
        $output->writeln('Manual invocation of ETL finished');
    }
}
