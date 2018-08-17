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
        $sql = "select o.user_id, count(d.id) as orders from `order` o, `menu_dish` md, `dish` d where o.day >= '{$monday}' AND o.day <= '{$friday}' and o.menu_dish_id = md.id AND md.dish_id = d.id GROUP BY o.user_id";
        foreach ($this->db->query($sql) as $row) {
            $stmt= $this->db->prepare("UPDATE user SET `is_feedback_active`=?, `feedback_count`=? WHERE `role`=? AND `id`=?");
            $stmt->execute([1, (int)$row['orders'], 'user', (int)$row['user_id']]);
            $count++;
        }
        $output->writeln("Feedback for users activated: {$count}");
    }
}