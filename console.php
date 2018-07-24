<?php
// application.php
 
require __DIR__.'/vendor/autoload.php';
 
use Symfony\Component\Console\Application;
 
try {
    $dbh = new PDO('mysql:host=127.0.0.1;dbname=wh;charset=utf8', 'root', 'root');
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

$application = new Application();

//Migrations commands 
$application->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand());
$application->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand());
$application->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand());
$application->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand());
$application->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand());
$application->add(new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand());
$application->add((new \App\Commands\ActivateFeedbackCommand())->setDB($dbh));
$application->add((new \App\Commands\NextWeekOrderReminderCommand())->setDB($dbh));
 
$application->run();