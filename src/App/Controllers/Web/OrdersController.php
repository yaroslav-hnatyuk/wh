<?php

namespace App\Controllers\Web;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class OrdersController
{
    protected $app;

    public function __construct(Application $app) 
    {
        $this->app = $app;
    }

    public function index()
    {
        $userRole = 'user'; //TODO: get user role after successful login

        return $this->app['twig']->render("order/{$userRole}.twig", array(
            'name' => $name,
        ));
    }
}
