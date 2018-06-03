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
        return $this->app['twig']->render("profile/user.twig", array(
            'userRole' => $this->getUser()->role
        ));
    }
}