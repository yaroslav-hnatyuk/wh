<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class MenudishesController extends BaseController
{

    protected $menuDishesService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->menuDishesService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->menuDishesService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->menuDishesService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->menuDishesService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->menuDishesService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->menuDishesService->delete($id));
    }
}
