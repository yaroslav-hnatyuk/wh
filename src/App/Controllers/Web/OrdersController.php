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
        $nextday = $request->query->get('nextday');

        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');
        $filterPeriod = $this->app['session']->get('filter_period') ?: FiltersController::FILTER_PERIOD_WEEK;
        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);

        if ($nextday) {
            $tomorrow = date('Y-m-d',strtotime("tomorrow"));
            $dayname = date('D', strtotime($tomorrow));
            if ($dayname === 'Sat' || $dayname === 'Sun') {
                if ($dayname === 'Sat') {
                    $tomorrow = date('Y-m-d',strtotime("+3 days"));
                }
                if ($dayname === 'Sun') {
                    $tomorrow = date('Y-m-d',strtotime("+2 days"));
                }
            }
        }

        $filters = array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $nextday ? $tomorrow : $period['start']['date'],
            'end_date' => $nextday ? $tomorrow : $period['end']['date']
        );

        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getOrdersByFilters($filters);

        list($menu, $totalByDaysAndUsers, $totalPriceInfo) = $this->ordersService->mergeMenuWithOrdersForExport($menu, $orders, $period);
        $users = $this->app['users.service']->getUsersByFilters($filters);

        $this->app['export.service']->createXlsReport($menu, $users, $totalByDaysAndUsers, $totalPriceInfo, array(
            'company' => $this->app['companies.service']->findOne($company),
            'office' => $this->app['offices.service']->findOne($office),
            'start_date' => $nextday ? $tomorrow : $period['start']['date'],
            'end_date' => $nextday ? $tomorrow : $period['end']['date']
        ));
    }

    private function userOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');
        $filterPeriod = $this->app['session']->get('filter_period') ?: 'week';
        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);

        $settingOrderHour = $this->app['settings.service']->getOneByName('order_hour');
        $menu = $this->menuService->getForPeriodForOrders($period);

        $orders = $this->ordersService->getUserOrdersByPeriod($this->getUser()->id, $period);
        list($menu, $totalByDays, $totalPriceInfo) = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
        $menu = $this->menuService->groupMenuDishes($menu, true);

        $disabledMonday = null;
        $dayname = date('D');
        $orderHour = $settingOrderHour ? intval($settingOrderHour['value']) : 0;
        if ($dayname === 'Fri' || $dayname === 'Sat' || $dayname === 'Sun') {
            if ($dayname === 'Fri' && (int)date('H') >= $orderHour) {
                $disabledMonday = date('Y-m-d',strtotime("+3 days"));
            }
            if ($dayname === 'Sat') {
                $disabledMonday = date('Y-m-d',strtotime("+2 days"));
            }
            if ($dayname === 'Sun') {
                $disabledMonday = date('Y-m-d',strtotime("+1 days"));
            }
        }

        return $this->app['twig']->render("order/user.twig", array(
            'period' => $period,
            'filterPeriod' => $filterPeriod,
            'userRole' => $this->getUser()->role,
            'menu' => $menu,
            'orderHour' => $settingOrderHour ? intval($settingOrderHour['value']) : 0, 
            'disabledMonday' => $disabledMonday,
            'totalByDays' => $totalByDays,
            'totalPriceInfo' => $totalPriceInfo
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

    private function managerAdminOrder(Request $request)
    {
        $week = $request->query->get('week');
        $year = $request->query->get('year');

        $company = $this->app['session']->get('filter_company');
        $office = $this->app['session']->get('filter_office');
        $user = $this->app['session']->get('filter_user');
        $filterPeriod = $this->app['session']->get('filter_period') ?: FiltersController::FILTER_PERIOD_WEEK;

        $period = $this->ordersService->getPeriodForYearAndWeek($year, $week, $filterPeriod);
        $menu = $this->menuService->getForPeriodForOrders($period);
        $orders = $this->ordersService->getOrdersByFilters(array(
            'company' => $company,
            'office' => $office,
            'user' => $user,
            'start_date' => $period['start']['date'],
            'end_date' => $period['end']['date']
        ));

        list($menu, $totalByDays, $totalPriceInfo) = $this->ordersService->mergeMenuWithOrders($menu, $orders, $period);
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
            'users' => $this->app['users.service']->getAllByOffice($office),
            'totalByDays' => $totalByDays,
            'totalPriceInfo' => $totalPriceInfo
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
