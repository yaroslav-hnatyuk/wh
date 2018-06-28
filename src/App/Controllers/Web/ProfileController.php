<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class ProfileController extends BaseController
{
    public function __construct(Application $app) 
    {
        $this->app = $app;
    }

    public function index()
    {
        return $this->app['twig']->render("profile/index.twig", array(
            'active' => 'index',
            'userRole' => $this->getUser()->role,
            'user' => $this->getUser(),
            'reminders_count' => $this->getUser()->reminders,
            'feedback_count' => $this->getUser()->feedback_count
        ));
    }

    public function feedback()
    {
        $dishes = array();
        if ($this->getUser()->is_feedback_active) {
            $period = $this->app['orders.service']->getPeriodForYearAndWeek();
            $period = $this->app['orders.service']->getPeriodForYearAndWeek($period['prev']['year'], $period['prev']['week']);
    
            $menu = $this->app['menudishes.service']->getForPeriodForOrders($period);
            $orders = $this->app['orders.service']->getUserOrdersByPeriod($this->getUser()->id, $period);
            $menuIds = array();
            foreach ($orders as $order) {
                $menuIds[$order['menu_dish_id']] = $order['menu_dish_id'];
            }
    
            foreach ($menu as $dish) {
                if (isset($menuIds[$dish['menu_id']])) {
                    $dishes[] = $dish;
                }
            }
        }
        
        return $this->app['twig']->render("profile/feedback.twig", array(
            'active' => 'feedback',
            'userRole' => $this->getUser()->role,
            'dishes' => $dishes,
            'reminders_count' => $this->getUser()->reminders,
            'feedback_count' => $this->getUser()->feedback_count
        ));
    }

    public function reminders()
    {
        $this->app['users.service']->clearReminders($this->getUser()->id);
        return $this->app['twig']->render("profile/reminders.twig", array(
            'active' => 'reminders',
            'userRole' => $this->getUser()->role,
            'reminders' => $this->app['reminders.service']->getAll(),
            'reminders_count' => 0,
            'feedback_count' => $this->getUser()->feedback_count
        ));
    }

    public function promo()
    {
        return $this->app['twig']->render("profile/promo.twig", array(
            'active' => 'promo',
            'userRole' => $this->getUser()->role,
            'reminders_count' => $this->getUser()->reminders,
            'feedback_count' => $this->getUser()->feedback_count
        ));
    }
}
