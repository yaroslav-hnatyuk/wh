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

    public function index(Request $request)
    {
        $handler = $this->getUser()->role . "Order";
        return $this->$handler($request);
    }

    private function userOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week);

        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getUserOrdersByPeriod($this->getUser()->id, $period);
        $menu = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        $menu = $this->menuService->groupMenuDishes($menu, true);

        return $this->app['twig']->render("order/user.twig", array(
            'period' => $period,
            'userRole' => $this->getUser()->role,
            'menu' => $menu
        ));
    }

    private function managerOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');

        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');

        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week);
        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getOrdersByFilters(array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        ));

        $menu = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        $menu = $this->menuService->groupMenuDishes($menu, true);

        return $this->app['twig']->render("order/manager.twig", array(
            'period' => $period,
            'userRole' => $this->getUser()->role,
            'menu' => $menu,
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'companies' => $this->app['companies.service']->getAll(),
            'offices' => $this->app['offices.service']->getAllByCompany($company),
            'users' => $this->app['users.service']->getAllByOffice($office)
        ));
    }

    private function adminOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week);
        // $menu = $this->menuService->getForPeriodForOrders($period);
        // $orders = $this->ordersService->getUserOrdersByPeriod($this->getUser()->id, $period);
        // $menu = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        // $menu = $this->menuService->groupMenuDishes($menu, true);

        return $this->app['twig']->render("order/admin.twig", array(
            'period' => $period,
            'userRole' => $this->getUser()->role
            // 'menu' => $menu
        ));
    }
}
