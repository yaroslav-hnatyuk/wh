<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
 
class NextWeekOrderReminderCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('next:week:order:reminder')
             ->setDescription('Send reminders about order for next week');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Send reminders about order for next week");
    }
}