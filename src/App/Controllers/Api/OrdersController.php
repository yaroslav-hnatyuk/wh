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

    public function getOne($id)
    {
        return new JsonResponse($this->ordersService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->ordersService->getAll());
    }

    public function save(Request $request)
    {
        $orders = json_decode($request->getContent(), true);
        $this->ordersService->saveUserOrders($this->getUser()->id, $orders);
        return new JsonResponse(
            // $this->ordersService->save($orders)
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->ordersService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->ordersService->delete($id));
    }
}
