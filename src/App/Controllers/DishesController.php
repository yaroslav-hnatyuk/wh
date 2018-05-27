<?php

namespace App\Controllers;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DishesController
{

    protected $dishesService;

    public function __construct($service)
    {
        $this->dishesService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->dishesService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->dishesService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->dishesService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->dishesService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->dishesService->delete($id));
    }
}
