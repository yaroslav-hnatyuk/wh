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
        $this->checkPermissions(array('user'));
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
        $this->checkPermissions(array('user'));
        $dishes = array();
        if ($this->getUser()->is_feedback_active) {
            $period = $this->app['orders.service']->getPeriodForYearAndWeek();
            $menu = $this->app['menudishes.service']->getForPeriodForOrders($period);
            $orders = $this->app['orders.service']->getUserOrdersGroupsByPeriod($this->getUser()->id, $period);

            $groups = array();
            foreach ($orders as $order) {
                if (!isset($groups[$order['group_id']])) {
                    $groups[$order['group_id']] = $this->app['dishgroups.service']->findOne($order['group_id']);
                    $groups[$order['group_id']]['dishes'] = array();
                }
            }

            foreach ($menu as $dish) {
                if (isset($groups[$dish['group_id']])) {
                    $groups[$dish['group_id']]['dishes'][] = array(
                        'dish_id' => $dish['dish_id'],
                        'dish_name' => $dish['dish_name'],
                        'description' => $dish['description']
                    );   
                }
            }

            $this->app['users.service']->clearFeedbacks($this->getUser()->id);
        }
        
        return $this->app['twig']->render("profile/feedback.twig", array(
            'active' => 'feedback',
            'userRole' => $this->getUser()->role,
            'groups' => $groups,
            'reminders_count' => $this->getUser()->reminders,
            'feedback_count' => 0
        ));
    }

    public function reminders()
    {
        $this->checkPermissions(array('user'));
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
        $this->checkPermissions(array('user'));
        return $this->app['twig']->render("profile/promo.twig", array(
            'active' => 'promo',
            'userRole' => $this->getUser()->role,
            'reminders_count' => $this->getUser()->reminders,
            'feedback_count' => $this->getUser()->feedback_count
        ));
    }
}
