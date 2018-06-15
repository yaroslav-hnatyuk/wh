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

    public function export(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');

        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');
        $filterPeriod = $this->app['session']->get('filter_period') ?: FiltersController::FILTER_PERIOD_WEEK;

        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week);
        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getOrdersByFilters(array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        ));

        list($menu, $totalCountByDays, $totalPrice) = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);

        //TODO get users who is present in orders by period and by office, company, user
        $users = $this->app['users.service']->getAll();

        $this->app['export.service']->createReport($menu, $users);
    }

    private function userOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        $filterPeriod = $this->app['session']->get('filter_period') ?: 'week';
        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);

        $menu = $this->menuService->getForPeriodForOrders($period);

        $orders = $this->ordersService->getUserOrdersByPeriod($this->getUser()->id, $period);
        list($menu, $totalByDays, $totalPrice) = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        $menu = $this->menuService->groupMenuDishes($menu, true);

        return $this->app['twig']->render("order/user.twig", array(
            'period' => $period,
            'filterPeriod' => $filterPeriod,
            'userRole' => $this->getUser()->role,
            'menu' => $menu,
            'totalByDays' => $totalByDays,
            'totalPrice' => $totalPrice
        ));
    }

    private function managerAdminOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');

        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');
        $filterPeriod = $this->app['session']->get('filter_period') ?: FiltersController::FILTER_PERIOD_WEEK;

        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week);
        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getOrdersByFilters(array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        ));

        list($menu, $totalCountByDays, $totalPrice) = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        $menu = $this->menuService->groupMenuDishes($menu, true);

        return $this->app['twig']->render("order/manager.twig", array(
            'period' => $period,
            'userRole' => $this->getUser()->role,
            'menu' => $menu,
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'year' => $year,
            'week' => $week,
            'filterPeriod' => $filterPeriod,
            'companies' => $this->app['companies.service']->getAll(),
            'offices' => $this->app['offices.service']->getAllByCompany($company),
            'users' => $this->app['users.service']->getAllByOffice($office)
        ));
    }

    private function managerOrder(Request $request)
    {
        return $this->managerAdminOrder($request);
    }

    private function adminOrder(Request $request)
    {
        return $this->managerAdminOrder($request);
    }
}
