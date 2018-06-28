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
        $stmt= $this->db->prepare("INSERT INTO reminder (`text`, `created`) VALUES ('Замовляйте обіди на наступний тиждень. Смачного Вам і ганих вихідних! :)', DATE_FORMAT(NOW(),'%Y-%m-%d'))");
        $stmt->execute([1, 'user']);

        $stmt= $this->db->prepare("UPDATE user SET `reminders`=? WHERE `role`=?");
        $stmt->execute([1, 'user']);

        $output->writeln("Reminder about order for the next week sent");
    }
}