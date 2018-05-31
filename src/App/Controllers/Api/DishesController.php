<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class DishesController extends BaseController
{

    protected $dishesService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
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
