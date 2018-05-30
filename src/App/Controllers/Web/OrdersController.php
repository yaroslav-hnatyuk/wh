<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class OrdersController extends BaseController
{
    private $ordersService;

    public function __construct(Application $app, $ordersService) 
    {
        $this->app = $app;
        $this->ordersService = $ordersService;
    }

    public function index()
    {
        return $this->app['twig']->render("order/{$this->getUser()->role}.twig", array(
            'currentPeriod' => $this->ordersService->getCurrentPeriod()
        ));
    }
}
