<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class OrdersController
{

    protected $ordersService;

    public function __construct($service)
    {
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
        return new JsonResponse(
            $this->ordersService->save($request->request->all())
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
