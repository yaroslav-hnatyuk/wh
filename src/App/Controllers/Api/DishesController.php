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

    public function getOne(Request $request, $id)
    {
        $included = $request->query->get('included') ?: array();
        $dish = $this->dishesService->getOne($id);

        if (in_array('reviews_count', $included, TRUE)) {
            $dish['reviews_count'] = $this->app['feedback.service']->getCountByDishId($dish['id']);
        }
        if (in_array('rating', $included, TRUE)) {
            $dish['rating'] = $this->app['ratings.service']->getAverageByDishId($dish['id']);
        }

        return new JsonResponse($dish);
    }

    public function getAll()
    {
        return new JsonResponse($this->dishesService->getAll());
    }

    public function save(Request $request)
    {
        $dishes = json_decode($request->getContent(), true);
        return new JsonResponse(
            $this->dishesService->saveDishes($dishes)
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
