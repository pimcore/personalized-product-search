<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\Command;

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
        parent::__construct();
        $this->purchaseHistory = $purchaseHistory;
        $this->logger = $logger;
    }

    public function configure()
    {
        $this
            ->setName('personalizedsearch:start-etl')
            ->setDescription('Triggers the ETL mechanism that loads the purchase history data for all users');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->logger->info('Invocation of PurchaseHistory ETL started');
            $output->writeln('Invocation of PurchaseHistory ETL started');

            $this->purchaseHistory->updateOrderIndexFromOrderDb();

            $this->logger->info('Invocation of PurchaseHistory ETL finished');
            $output->writeln('Invocation of PurchaseHistory ETL finished');
        } catch (\Exception $exception) {
            $this->logger->error('Invocation of PurchaseHistory ETL failed with message: ' . $exception->getMessage());
            $output->writeln('Invocation of PurchaseHistory ETL failed with message: ' . $exception->getMessage());
        }
    }
}
