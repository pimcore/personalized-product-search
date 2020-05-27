<?php

namespace Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\Command;

use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\CustomerGroupInterface;
use Pimcore\Bundle\PersonalizedSearchBundle\ExtractTransformLoad\PurchaseHistoryInterface;
use Pimcore\Console\AbstractCommand;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ETLCommand extends AbstractCommand
{
    private $purchaseHistory;
    private $customerGroup;
    private $logger;

    public function __construct(PurchaseHistoryInterface $purchaseHistory, CustomerGroupInterface $customerGroup, LoggerInterface $logger)
    {
        parent::__construct();
        $this->purchaseHistory = $purchaseHistory;
        $this->customerGroup = $customerGroup;
        $this->logger = $logger;
    }

    public function configure()
    {
        $this
            ->setName('personalizedsearch:start-etl')
            ->setDescription('Triggers the ETL mechanism that loads the purchase history data for all users')
            ->addArgument('option', InputArgument::OPTIONAL, 'Invoke PurchaseHistory or CustomerGroup seperately');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $argument = $input->getArgument('option');
            switch ($argument)
            {
                case 'PurchaseHistory':
                    $this->log('PurchaseHistory Invocation started', $output);
                    $this->purchaseHistory->updateOrderIndexFromOrderDb();
                    $this->log('PurchaseHistory Invocation finished', $output);
                    break;
                case 'CustomerGroup':
                    $this->log('CustomerGroup Invocation started', $output);
                    $this->customerGroup->updateCustomerGroupAndSegmentsIndicesFromOrderDb();
                    $this->log('CustomerGroup Invocation finished', $output);
                    break;
                default:
                    $this->log('ETL full Invocation started', $output);
                    $this->purchaseHistory->updateOrderIndexFromOrderDb();
                    $this->customerGroup->updateCustomerGroupAndSegmentsIndicesFromOrderDb();
                    $this->log('ETL full Invocation finished', $output);
                    break;
            }
        } catch (\Exception $exception) {
            $this->logger->error('Invocation of PurchaseHistory ETL failed with message: ' . $exception->getMessage());
            $output->writeln('Invocation of PurchaseHistory ETL failed with message: ' . $exception->getMessage());
        }
    }

    private function log($message, OutputInterface $output)
    {
        $this->logger->info($message);
        $output->writeln($message);
    }
}
