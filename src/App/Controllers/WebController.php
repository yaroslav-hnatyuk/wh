<?php

namespace App\Controllers;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class WebController
{
    protected $app;

    public function __construct(Application $app) 
    {
        $this->app = $app;
    }

    public function login()
    {
        return $this->app['twig']->render('login/index.twig', array(
            'name' => $name,
        ));
    }
}
