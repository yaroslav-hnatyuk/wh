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

    public function save(Request $request)
    {
        $this->checkPermissions(array('admin', 'manager'));
        $menuDish = json_decode($request->getContent(), true);
        return new JsonResponse(
            $this->menuDishesService->save($menuDish)
        );
    }

    public function update($id, Request $request)
    {
        $this->checkPermissions(array('admin', 'manager'));
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->menuDishesService->update($data)
        );
    }

    public function delete($id)
    {
        $this->checkPermissions(array('admin', 'manager'));
        return new JsonResponse($this->menuDishesService->delete($id));
    }
}
