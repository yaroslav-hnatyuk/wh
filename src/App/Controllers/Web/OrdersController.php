<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class OrdersController extends BaseController
{
    private $ordersService;
    private $menuService;

    public function __construct(Application $app, $ordersService, $menuService) 
    {
        $this->app = $app;
        $this->ordersService = $ordersService;
        $this->menuService = $menuService;
    }

    public function index()
    {
        $period = $this->ordersService->getCurrentPeriod();
        $menu = $this->menuService->getForPeriod($period);
        $orders = $this->ordersService->getUserOrdersByPeriod($this->getUser()->id, $period);
        $menu = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        $menu = $this->menuService->groupMenuDishes($menu, true);

        return $this->app['twig']->render("order/{$this->getUser()->role}.twig", array(
            'period' => $period,
            'menu' => $menu
        ));
    }
}
