<?php

namespace App\Controllers\Web;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class DishesController extends BaseController
{
    private $dishesService = null;
    private $groupsService = null;

    public function __construct(Application $app, $dishesService, $groupsService) 
    {
        $this->app = $app;
        $this->dishesService = $dishesService;
        $this->groupsService = $groupsService;
    }

    public function index()
    {
        $this->checkPermissions(array('admin', 'manager'));

        $dishes = $this->dishesService->getAll();
        $dishes = $this->groupsService->groupDishes($dishes);

        return $this->app['twig']->render("dishes/index.twig", array(
            'userRole' => $this->getUser()->role,
            'dishes' => $dishes
        ));
    }
}
