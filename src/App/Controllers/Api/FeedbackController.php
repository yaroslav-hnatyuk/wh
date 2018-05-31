<?php

namespace App\Controllers\Api;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Controllers\BaseController;
use Silex\Application;

class FeedbackController extends BaseController
{

    protected $feedbackService;

    public function __construct(Application $app, $service)
    {
        $this->app = $app;
        $this->feedbackService = $service;
    }

    public function getOne($id)
    {
        return new JsonResponse($this->feedbackService->getOne($id));
    }

    public function getAll()
    {
        return new JsonResponse($this->feedbackService->getAll());
    }

    public function save(Request $request)
    {
        return new JsonResponse(
            $this->feedbackService->save($request->request->all())
        );
    }

    public function update($id, Request $request)
    {
        $data = $request->request->all();
        $data['id'] = $id;
        return new JsonResponse(
            $this->feedbackService->update($data)
        );
    }

    public function delete($id)
    {
        return new JsonResponse($this->feedbackService->delete($id));
    }
}
