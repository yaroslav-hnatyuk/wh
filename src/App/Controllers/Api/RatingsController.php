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
        return new JsonResponse(
            $this->ratingsService->save($request->request->all())
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
