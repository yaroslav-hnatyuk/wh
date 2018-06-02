<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class MenuController extends BaseController
{
    private $menuService;
    private $ordersService;

    public function __construct(Application $app, $menuService, $ordersService) 
    {
        $this->app = $app;
        $this->menuService = $menuService;
        $this->ordersService = $ordersService;
    }

    public function index()
    {
        $period = $this->ordersService->getCurrentPeriod();
        $menu = $this->menuService->getForPeriod($period, true);

        return $this->app['twig']->render("menu/index.twig", array(
            'userRole' => $this->getUser()->role,
            'period' => $period,
            'menu' => $menu
        ));
    }
}
