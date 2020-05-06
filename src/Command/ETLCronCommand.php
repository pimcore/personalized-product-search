<?php


namespace PersonalizedSearchBundle\src\Command;

use Pimcore\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class ETLCronCommand extends AbstractCommand
{
    public function configure()
    {
        $this
            ->setName('personalizedsearch:schedule:etl')
            ->setDescription('Registers the async invocation of the ETL')
            ->addArgument('hours', InputArgument::REQUIRED, 'hour interval for the cron job')
            ->addArgument('minutes', InputArgument::REQUIRED, 'minute interval for the cron job')
            ->addArgument('startTime', InputArgument::OPTIONAL, 'the start time [hour]:[minute] when the cron job executes the first time, argument is optional and current time is taken as default');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $hours = $input->getArgument('hours');
        $minutes = $input->getArgument('minutes');
        $startTime = $input->getArgument('startTime');

        if($startTime) {
            $output->writeln("custom startTime is set");
        }

        $commandResponse = shell_exec($minutes . " " . $hours . " * * * php bin/console personalizedsearch:start:etl &> /dev/null");

        $output->writeln($commandResponse);
        $output->writeln('Done with ETLCommand');
    }
}
