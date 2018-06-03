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
    private $dishesService;
    private $groupsService;

    public function __construct(
        Application $app, 
        $menuService, 
        $ordersService,
        $dishesService,
        $groupsService
    ) 
    {
        $this->app = $app;
        $this->menuService = $menuService;
        $this->ordersService = $ordersService;
        $this->dishesService = $dishesService;
        $this->groupsService = $groupsService;
    }

    public function index()
    {
        $period = $this->ordersService->getPeriodForYearAndWeek();
        $dishes = $this->dishesService->getAll();
        $menu = $this->menuService->getForPeriod($period, true);
        foreach ($dishes as &$dish) {
            if (isset($menu[$dish['id']])) {
                $dish['menu'] = $menu[$dish['id']];
            }
        }
        unset($dish);
        unset($menu);

        return $this->app['twig']->render("menu/index.twig", array(
            'userRole' => $this->getUser()->role,
            'dishes' => $this->groupsService->groupDishes($dishes),
            'period' => $period
        ));
    }
}
