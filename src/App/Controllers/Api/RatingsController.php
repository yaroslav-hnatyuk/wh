<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class RatingsController extends BaseController
{

    protected $ratingsService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->ratingsService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->ratingsService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->ratingsService->getAll());
    }

    public function save(Request $request)
    {
        $rating = array(
            'user_id' => $this->getUser()->id,
            'dish_id' => $request->request->get('dish_id'),
            'mark' => $request->request->get('mark')
        );
        
        return new JsonResponse(
            $this->ratingsService->save($rating)
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->ratingsService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->ratingsService->delete($id));
    }
}
