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
            'user' => $this->getUser()
        ));
    }

    public function feedback()
    {
        return $this->app['twig']->render("profile/feedback.twig", array(
            'active' => 'feedback',
            'userRole' => $this->getUser()->role
        ));
    }

    public function reminders()
    {
        return $this->app['twig']->render("profile/reminders.twig", array(
            'active' => 'reminders',
            'userRole' => $this->getUser()->role
        ));
    }

    public function promo()
    {
        return $this->app['twig']->render("profile/promo.twig", array(
            'active' => 'promo',
            'userRole' => $this->getUser()->role
        ));
    }
}
