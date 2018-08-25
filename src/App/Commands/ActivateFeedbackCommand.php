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
        $monday = date( 'Y-m-d', strtotime( 'monday this week' ) );
        $friday = date( 'Y-m-d', strtotime( 'friday this week' ) );

        $count = 0;
        $sql = "SELECT o.user_id, (SELECT count(dish.id) FROM `dish` WHERE dish.dish_group_id = o.group_id) as orders FROM `order_group` o WHERE o.day >= '{$monday}' AND o.day <= '{$friday}' GROUP BY o.user_id, o.group_id";
        $users = array();
        foreach ($this->db->query($sql) as $row) {
            if (!isset($users[$row['user_id']])) {
                $users[$row['user_id']] = 0;
            }
            $users[$row['user_id']] += $row['orders'];
        }

        foreach ($users as $userId => $ordersCount) {
            $stmt= $this->db->prepare("UPDATE user SET `is_feedback_active`=?, `feedback_count`=? WHERE `role`=? AND `id`=?");
            $stmt->execute([1, (int)$ordersCount, 'user', (int)$userId]);
            $count++;
        }

        $output->writeln("Feedback for users activated: {$count}");
    }
}