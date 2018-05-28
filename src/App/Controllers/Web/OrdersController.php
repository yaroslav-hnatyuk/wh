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
        return $this->app['twig']->render('login/index.twig', array(
            'name' => $name,
        ));
    }

    public function login()
    {
        return $this->app['twig']->render('login/index.twig', array(
            'name' => $name,
        ));
    }
}
