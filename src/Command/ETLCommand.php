<?php

namespace PersonalizedSearchBundle\src\Command;

use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PurchaseHistoryInterface;
use Pimcore\Console\AbstractCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ETLCommand extends AbstractCommand
{
    private $purchaseHistory;
    private $logger;

    public function __construct(PurchaseHistoryInterface $purchaseHistory, LoggerInterface $logger)
    {
        $this->purchaseHistory = $purchaseHistory;
        $this->logger = $logger;
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
        try {
            $this->purchaseHistory->updateOrderIndexFromOrderDb();

            $this->logger->info('Invocation of PurchaseHistory ETL finished');
            $output->writeln('Invocation of PurchaseHistory ETL finished');
        } catch (\Exception $exception) {
            $this->logger->error('Invocation of PurchaseHistory ETL failed with message: ' . $exception->getMessage());
            $output->writeln('Invocation of PurchaseHistory ETL failed with message: ' . $exception->getMessage());
        }
    }
}
