<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
 
class ActivateFeedbackCommand extends BaseCommand
{
    protected function configure()
    {
        $this->setName('activate:feedback')
             ->setDescription('Activate feedback for users');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stmt= $this->db->prepare("UPDATE user SET `is_feedback_active`=? WHERE `role`=?");
        $stmt->execute([1, 'user']);
        $output->writeln("Feedback for users activated");
    }
}