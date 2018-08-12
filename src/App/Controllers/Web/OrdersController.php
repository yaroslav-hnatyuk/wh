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

    public function main(Request $request)
    {
        $handler = $this->getUser()->role . "Order";
        return $this->$handler($request);
    }

    public function index(Request $request)
    {
        $handler = $this->getUser()->role . "OrderGroup";
        return $this->$handler($request);
    }

    public function export(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        
        $filterPeriod = $this->app['session']->get('filter_period') ?: FiltersController::FILTER_PERIOD_WEEK;
        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');
        $filters = array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        );

        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);
        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getOrdersGroupsByFilters($filters);

        list($menu, $totalByDaysAndUsers, $totalPriceInfo) = $this->ordersService->mergeMenuWithOrdersGroups($menu, $orders, $period, true);
        $users = $this->app['users.service']->getUsersByFilters($filters);

        $this->app['export.service']->createXlsReport($menu, $users, $totalByDaysAndUsers, $totalPriceInfo, array(
            'company' => $this->app['companies.service']->findOne($company),
            'office' => $this->app['offices.service']->findOne($office),
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        ));
    }

    private function userOrderGroup(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        $filterPeriod = $this->app['session']->get('filter_period') ?: 'week';
        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);

        $settingOrderHour = $this->app['settings.service']->getOneByName('order_hour');
        $menu = $this->menuService->getForPeriodForOrders($period);

        $orders = $this->ordersService->getUserOrdersGroupsByPeriod($this->getUser()->id, $period);
        list($menu, $totalByDays, $totalPriceInfo) = $this->ordersService->mergeMenuWithOrdersGroups($menu, $orders, $period);

        return $this->app['twig']->render("order/user.twig", array(
            'period' => $period,
            'filterPeriod' => $filterPeriod,
            'userRole' => $this->getUser()->role,
            'menu' => $menu,
            'orderHour' => $settingOrderHour ? intval($settingOrderHour['value']) : 0, 
            'disabledMonday' => $this->ordersService->getDisabledMonday(),
            'totalByDays' => $totalByDays,
            'totalPriceInfo' => $totalPriceInfo
        ));
    }

    private function managerAdminOrderGroup(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        
        $filterPeriod = $this->app['session']->get('filter_period') ?: FiltersController::FILTER_PERIOD_WEEK;
        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');

        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);
        $menu = $this->menuService->getForPeriodForOrders($period);

        $orders = $this->ordersService->getOrdersGroupsByFilters(array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        ));

        list($menu, $totalByDays, $totalPriceInfo) = $this->ordersService->mergeMenuWithOrdersGroups($menu, $orders, $period);

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
            'users' => $this->app['users.service']->getAllByOffice($office),
            'totalByDays' => $totalByDays,
            'totalPriceInfo' => $totalPriceInfo
        ));
    }

    private function managerOrderGroup(Request $request)
    {
        return $this->managerAdminOrderGroup($request);
    }

    private function adminOrderGroup(Request $request)
    {
        return $this->managerAdminOrderGroup($request);
    }

}
