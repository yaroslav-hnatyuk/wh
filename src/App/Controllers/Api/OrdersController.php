<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;


class OrdersController extends BaseController
{

    protected $ordersService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->ordersService = $service;
    }

    public function save(Request $request)
    {
        $this->checkPermissions(array('user'));
        $orders = json_decode($request->getContent(), true);
        $this->ordersService->saveUserOrdersGroups($this->getUser()->id, $orders);
        return new JsonResponse();
    }

}
